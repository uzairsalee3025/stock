<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientVisitController extends Controller
{
    /** Add a repeat / follow-up visit to an existing patient. */
    public function store(Request $request, Patient $patient)
    {
        $data = $this->validateVisit($request);
        $data['prescription_path'] = $this->storePrescription($request);

        $patient->visits()->create($data);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'New visit added to patient history.');
    }

    public function edit(PatientVisit $visit)
    {
        $visit->load('patient');

        return view('patients.visits.edit', compact('visit'));
    }

    public function update(Request $request, PatientVisit $visit)
    {
        $data = $this->validateVisit($request);

        if ($request->hasFile('prescription')) {
            if ($visit->prescription_path) {
                Storage::disk('public')->delete($visit->prescription_path);
            }
            $data['prescription_path'] = $this->storePrescription($request);
        }

        $visit->update($data);

        return redirect()
            ->route('patients.show', $visit->patient_id)
            ->with('success', 'Visit updated.');
    }

    public function destroy(PatientVisit $visit)
    {
        $patientId = $visit->patient_id;

        if ($visit->prescription_path) {
            Storage::disk('public')->delete($visit->prescription_path);
        }
        $visit->delete();

        return redirect()
            ->route('patients.show', $patientId)
            ->with('success', 'Visit removed.');
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

    private function storePrescription(Request $request): ?string
    {
        if ($request->hasFile('prescription')) {
            return $request->file('prescription')->store('prescriptions', 'public');
        }

        return null;
    }
}
