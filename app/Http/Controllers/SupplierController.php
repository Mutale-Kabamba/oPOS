<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\SpreadsheetReaderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupplierController extends Controller
{
    public function __construct(private readonly SpreadsheetReaderService $spreadsheetReader)
    {
    }

    public function index()
    {
        $q = trim((string) request('q', ''));
        $status = (string) request('status', 'all');

        $suppliers = Supplier::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($builder) use ($q) {
                    $builder->where('name', 'like', "%{$q}%")
                        ->orWhere('contact_person', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.suppliers.index', compact('suppliers', 'q', 'status'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateSupplier($request);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        Supplier::create($data);

        return redirect()->route('admin.suppliers.index')->with('status', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $this->validateSupplier($request, $supplier);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $supplier->update($data);

        return redirect()->route('admin.suppliers.index')->with('status', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->transactions()->exists()) {
            return redirect()->route('admin.suppliers.index')->with('status', 'Cannot delete supplier with existing transactions. Deactivate instead.');
        }

        $supplier->delete();

        return redirect()->route('admin.suppliers.index')->with('status', 'Supplier deleted successfully.');
    }

    public function toggle(Supplier $supplier)
    {
        $supplier->update(['is_active' => ! $supplier->is_active]);

        return redirect()->route('admin.suppliers.index')->with('status', 'Supplier status updated.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'import_file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        $rows = $this->spreadsheetReader->readRows($request->file('import_file'));

        if (count($rows) === 0) {
            return back()->with('status', 'Import file is empty.');
        }

        $errors = [];
        $created = 0;
        $updated = 0;

        try {
            DB::transaction(function () use ($rows, &$errors, &$created, &$updated): void {
                foreach ($rows as $row) {
                    $rowNumber = (int) ($row['__row'] ?? 0);

                    $payload = [
                        'name' => $row['name'] ?? null,
                        'contact_person' => $row['contact_person'] ?? null,
                        'email' => $row['email'] ?? null,
                        'phone' => $row['phone'] ?? null,
                        'address' => $row['address'] ?? null,
                        'is_active' => $this->toBoolean($row['is_active'] ?? null),
                    ];

                    $validator = Validator::make($payload, [
                        'name' => ['required', 'string', 'max:255'],
                        'contact_person' => ['nullable', 'string', 'max:255'],
                        'email' => ['nullable', 'email', 'max:255'],
                        'phone' => ['nullable', 'string', 'max:40'],
                        'address' => ['nullable', 'string', 'max:255'],
                        'is_active' => ['nullable', 'boolean'],
                    ]);

                    if ($validator->fails()) {
                        $errors[] = "Row {$rowNumber}: {$validator->errors()->first()}";

                        continue;
                    }

                    $validated = $validator->validated();
                    $validated['is_active'] = (bool) ($validated['is_active'] ?? true);

                    $existing = Supplier::query()->where('name', $validated['name'])->first();

                    if ($existing) {
                        $existing->update($validated);
                        $updated++;
                    } else {
                        Supplier::query()->create($validated);
                        $created++;
                    }
                }

                if ($errors !== []) {
                    throw new \RuntimeException(implode(' | ', $errors));
                }
            });
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['import_file' => $exception->getMessage()])->withInput();
        }

        return back()->with('status', "Suppliers imported successfully. Created: {$created}, Updated: {$updated}.");
    }

    public function template(Request $request): StreamedResponse
    {
        $format = strtolower((string) $request->query('format', 'csv'));
        $headers = ['name', 'contact_person', 'email', 'phone', 'address', 'is_active'];
        $sampleRows = [
            ['Acme Supplies Ltd', 'Jane Banda', 'jane@acme.test', '+260977000001', 'Plot 12, Lusaka', 'true'],
            ['Green Recycling Partners', 'Peter Mwila', 'peter@green.test', '+260977000002', 'Industrial Area', 'true'],
        ];

        if ($format === 'xlsx') {
            return response()->streamDownload(function () use ($headers, $sampleRows): void {
                $spreadsheet = new Spreadsheet;
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->fromArray($headers, null, 'A1');
                $sheet->fromArray($sampleRows, null, 'A2');

                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, 'suppliers-import-template.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        return response()->streamDownload(function () use ($headers, $sampleRows): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            foreach ($sampleRows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 'suppliers-import-template.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function validateSupplier(Request $request, ?Supplier $supplier = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('suppliers', 'name')->ignore($supplier?->id)],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function toBoolean(?string $value): ?bool
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'active'], true);
    }
}