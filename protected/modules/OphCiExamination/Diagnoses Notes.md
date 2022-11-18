# Notes on Diagnoses

The current model for tracking diagnoses in examination (and subsequently at the patient level) is sub optimal. There is an ophthalmic diagnoses element and a systemic diagnoses element. These elements behave in similar but actually quite distinct ways. The following notes should be added to and expanded whilst this remains the case, in an effort to help any future developers with the challenges the current model presents.

## The overall principal

The overall approach is that when diagnoses are recorded in examination, these are surfaced up to the patient level by recording entries in the Episode (for principal diagnoses) or as SecondaryDiagnosis entries. Historically, this is rooted in a legacy data model that needed to support entering diagnosis both on patient summary screens and through the examination event. The efforts to maintain the synchronisation between these tables does not entirely account for all edge cases.

There are now views that can be used to reliably retrieve the latest state of diagnoses for the patient based on the most recent event that has been recorded, but the legacy models persist and are utilised in the application at various points.

## Ophthalmic diagnoses

Recorded in `Element_OphCiExamination_Diagnoses`, the storage of entries is managed through the older pattern of custom methods on `DefaultController` for the module. This specifically calls `updateDiagnoses` on the element model with POST data. Within this method, creation, update and deleting of the examination entries is carried out. And if the element is considered to be at the "tip" i.e. the latest entry, then the ophthalmic diagnoses recorded on the patient will also be updated based on those entries. This will use the `addDiagnosis` method on the `Patient` to create any new diagnoses that aren't principal, and will remove diagnoses that have been removed from the examination that have a relation to an existing `SecondaryDiagnosis`.

## Systemic diagnoses

Recorded in `SystemicDiagnoses` this uses the widget pattern and auto_relations for saving the entries. However, some challenging design patterns have been used within the widget and the models regarding diagnoses for which side is not relevant. It also specifically does not use the `Patient` methods for saving `SecondaryDiagnoses`, instead directly storing them from within entry model of `SystemicDiagnoses_Diagnosis`

## Handling delete behaviour

These notes have been written to summarise the information gleaned whilst attempting to handle a bug that has emerged from this approach. Namely that if the most recent examination containing new diagnoses is deleted from a patient, the `SecondaryDiagnosis` and principal diagnosis data for the patient will not be reverted back to the state that existed prior to that examination being created.

In an effort to resolve this, the `UpdatePatientDiagnosesAfterSoftDelete` invokable class has been created that can be triggered by the soft delete of the examination. This handler essentially replays the application of diagnoses to the Patient from the previous examination, and removes any that are not a part of that record. It does this for both Ophthalmic and Systemic diagnoses.

### Future development

We may need the handling of calculating patient diagnostic state to be triggered under additional circumstance. If that is the case, the methods in the event listener should be extracted into a separate component that can be utilised by different handlers. This would look something like:

```
class PatientDiagnosisManager

public function setPrincipalDiagnosisFrom(?Element_OphCiExamination_Diagnoses $element = null, Subspecialty $subspecialty)
{}

public function setOphthalmicSecondaryDiagnosesFrom(Element_OphCiExamination_Diagnoses $element)
{}

public function setSystemicDiagnosesFrom(?SystemicDiagnoses = null)
{}
```

## Test coverage

A high level set of tests has been written to cover this functionality. The factories and test helpers that have been defined to support this will reveal some of the details of the implementation. If more edge cases are discovered, these notes should be updated, and the test coverage expanded accordingly.