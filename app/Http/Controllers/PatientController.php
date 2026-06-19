<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PatientController extends Controller
{
    /** List + search/filter from the top filter bar. */
    public function index(Request $request)
    {
        $query = Patient::query()->with('latestVisit');

        // Generic keyword search across serial, name, phone.
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by doctor / status / visit date — these live on visits.
        $doctor = trim((string) $request->input('doctor'));
        $status = $request->input('status');
        $date = $request->input('date');

        if ($doctor || $status || $date) {
            $query->whereHas('visits', function ($q) use ($doctor, $status, $date) {
                if ($doctor) {
                    $q->where('doctor_name', 'like', "%{$doctor}%");
                }
                if ($status) {
                    $q->where('status', $status);
                }
                if ($date) {
                    $q->whereDate('visit_date', $date);
                }
            });
        }

        $patients = $query->latest()->paginate(15)->withQueryString();

        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create', [
            'serial' => Patient::generateSerialNumber(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePatient($request);
        $visitData = $this->validateVisit($request);

        $patient = DB::transaction(function () use ($data, $visitData, $request) {
            $patient = Patient::create($data + [
                'serial_number' => Patient::generateSerialNumber(),
            ]);

            $visitData['prescription_path'] = $this->storePrescription($request);
            $patient->visits()->create($visitData);

            return $patient;
        });

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', "Patient created. Serial number: {$patient->serial_number}");
    }

    public function show(Patient $patient)
    {
        $patient->load(['visits']);

        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $patient->update($this->validatePatient($request));

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Patient details updated.');
    }

    public function destroy(Patient $patient)
    {
        // Clean up any uploaded prescription files first.
        foreach ($patient->visits as $visit) {
            if ($visit->prescription_path) {
                Storage::disk('public')->delete($visit->prescription_path);
            }
        }
        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient record deleted.');
    }

    private function validatePatient(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'age' => ['nullable', 'integer', 'min:0', 'max:200'],
            'gender' => ['nullable', 'in:male,female,other'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);
    }

    private function validateVisit(Request $request): array
    {
        return $request->validate([
            'visit_date' => ['required', 'date'],
            'doctor_name' => ['nullable', 'string', 'max:255'],
            'disease' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'follow_up_date' => ['nullable', 'date', 'after_or_equal:visit_date'],
            'status' => ['required', 'in:active,follow_up,completed,cancelled'],
            'prescription' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ], [], ['prescription' => 'prescription slip']);
    }

    /** Store an uploaded prescription slip (image or PDF) and return its path. */
    private function storePrescription(Request $request): ?string
    {
        if ($request->hasFile('prescription')) {
            return $request->file('prescription')->store('prescriptions', 'public');
        }

        return null;
    }
}
