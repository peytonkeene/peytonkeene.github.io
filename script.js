document.addEventListener('DOMContentLoaded', function () {
    // Function to toggle the visibility of the Pain section based on the checkbox
    function togglePainFields() {
        const painSection = document.getElementById('painFields');
        painSection.style.display = document.getElementById('painCheck').checked ? 'block' : 'none';
    }

    // Attach event listener to the Pain checkbox
    document.getElementById('painCheck').addEventListener('change', togglePainFields);
    togglePainFields(); // Call to set initial visibility based on checkbox status

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
        const arrivalNarrative = `<h2>Arrival</h2>Upon EMS arrival, the patient was found to be ${patientPosition} ${additionalInfo}. The patient is alert and oriented x${orientationLevel}. The patient's GCS is ${gcs}. The patient appears ${patientAppearance}.`;

        const chiefComplaint = document.getElementById('chiefComplaint').value;
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

        const historyDetails = document.getElementById('historyDetails').value;
        const historyNarrative = `<h2>History</h2>The patient's past medical history, medications, and allergies are noted in the patient demographic tab: ${historyDetails}`;

        const patientOrientation = document.getElementById('patientOrientation').value;
        const patientAirway = document.getElementById('patientAirway').value;
        const patientBreathing = document.getElementById('patientBreathing').value;
        const patientCirculation = document.getElementById('patientCirculation').value;
        const skinConditions = Array.from(document.querySelectorAll('input[name="skinCondition"]:checked')).map(el => el.value).join(', ');
        const assessmentNarrative = `<h2>Assessment</h2>Please see the assessment tab for additional details. The patient is ${patientOrientation}. The patient's airway is ${patientAirway}. The patient's breathing is ${patientBreathing}. The patient's circulation is ${patientCirculation}. The patient's skin is ${skinConditions}.`;

        const fullNarrative = `${dispatchNarrative}<br>${arrivalNarrative}<br>${chiefComplaintNarrative}<br>${painNarrative}<br>${historyNarrative}<br>${assessmentNarrative}`;
        document.getElementById('narrativeOutput').innerHTML = fullNarrative;
    }

    // Attach event listener to the Generate Narrative button
    document.getElementById('generateButton').addEventListener('click', generateNarrative);

    // Function to reset the form and clear the narrative
    function resetForm() {
        document.getElementById('inputForm').reset();
        document.getElementById('narrativeOutput').innerHTML = '';
        togglePainFields();  // Reset pain section visibility
    }

    // Attach event listener to the Reset button
    document.getElementById('resetButton').addEventListener('click', resetForm);

    // Function to copy the narrative to the clipboard
    function copyToClipboard() {
        const narrativeText = document.getElementById('narrativeOutput').innerText;
        navigator.clipboard.writeText(narrativeText).then(function() {
            alert('Narrative copied to clipboard successfully!');
        }, function(err) {
            console.error('Could not copy narrative to clipboard: ', err);
            alert('Failed to copy narrative.');
        });
    }

    // Attach event listener to the Copy button
    document.getElementById('copyButton').addEventListener('click', copyToClipboard);
});
