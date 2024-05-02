document.addEventListener('DOMContentLoaded', function () {
    // Function to toggle the visibility of the Pain section
    function togglePainFields() {
        var painSection = document.getElementById('painFields');
        if (document.getElementById('painCheck').checked) {
            painSection.style.display = 'block';
        } else {
            painSection.style.display = 'none';
        }
    }

    document.getElementById('painCheck').addEventListener('change', togglePainFields);

    // Function to generate the narrative based on the form inputs
    function generateNarrative() {
        const emergenceCare = document.getElementById('emergenceCare').value;
        const responseType = document.getElementById('responseType').value;
        const dispatchDescription = document.getElementById('dispatchDescription').value;
        const dispatchNarrative = `<h2>Dispatch</h2>EC ${emergenceCare} was dispatched and responded ${responseType} to the above address for a: ${dispatchDescription}.`;

        const patientPosition = document.getElementById('patientPosition').value;
        const additionalInfo = document.getElementById('additionalInfo').value;
        const orientationLevel = document.getElementById('orientationLevel').value;
        const gcs = document.getElementById('gcs').value;
        const patientAppearance = document.getElementById('patientAppearance').value;
        const chiefComplaint = document.getElementById('chiefComplaint').value;

        const assessmentNarrative = `<h2>Assessment</h2>Upon EMS arrival, the patient was found to be ${patientPosition} ${additionalInfo}. The patient is alert and oriented x${orientationLevel}. The patient's GCS is ${gcs}. The patient appears ${patientAppearance}.`;
        const chiefComplaintNarrative = `<h2>Chief Complaint</h2>${chiefComplaint}`;

        let painNarrative = '';
        if (document.getElementById('painCheck').checked) {
            const painOnset = document.getElementById('painOnset').value;
            const provocationWhat = document.getElementById('provocationWhat').value;
            const provocationEffect = document.getElementById('provocationEffect').value;
            const painQuality = document.getElementById('painQuality').value;
            const painRadiation = document.getElementById('painRadiation').value;
            const painSeverity = document.getElementById('painSeverity').value;
            const painDuration = document.getElementById('painDuration').value;
            const painTimeUnit = document.getElementById('painTimeUnit').value;

            painNarrative = `<h2>Pain</h2>Pain Assessment: Onset is ${painOnset}. Provocation: ${provocationWhat} makes the pain ${provocationEffect}. Quality: ${painQuality}. Radiation: ${painRadiation}. Severity is ${painSeverity}. Time: ${painDuration} ${painTimeUnit}.`;
        }

        const fullNarrative = `${dispatchNarrative}<br>${assessmentNarrative}<br>${chiefComplaintNarrative}<br>${painNarrative}`;
        document.getElementById('narrativeOutput').innerHTML = fullNarrative;
    }

    document.getElementById('generateButton').addEventListener('click', generateNarrative);
    document.getElementById('resetButton').addEventListener('click', resetForm);
});

function resetForm() {
    document.getElementById('inputForm').reset();
    document.getElementById('narrativeOutput').innerHTML = '';
    document.getElementById('painFields').style.display = 'none';
    document.getElementById('painCheck').checked = false;
}
