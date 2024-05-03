document.addEventListener('DOMContentLoaded', function () {
    // Function to toggle the visibility of the Pain section
    function togglePainFields() {
        var painSection = document.getElementById('painFields');
        if (document.getElementById('painCheck').checked) {
            painSection.style.display = 'block'; // Show only if checked
        } else {
            painSection.style.display = 'none'; // Hide if not checked
        }
    }

    // Attach event listener to the Pain Check checkbox
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

        const assessmentNarrative = `<h2>Assessment</h2>Upon EMS arrival, the patient was found to be ${patientPosition} ${additionalInfo}. The patient is alert and oriented x${orientationLevel}. The patient's GCS is ${gcs}. The patient appears ${patientAppearance}.`;

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

        const detailedAssessmentNarrative = `<h2>Additional Assessment</h2>Please see the assessment tab for additional details. The patient is ${document.getElementById('patientOrientation').value}. The patient's airway is ${document.getElementById('patientAirway').value}. The patient's breathing is ${document.getElementById('patientBreathing').value}. The patient's circulation is ${document.getElementById('patientCirculation').value}. The patient's skin is ${Array.from(document.querySelectorAll('input[name="skinCondition"]:checked')).map(el => el.value).join(', ')}.`;

        const fullNarrative = `${dispatchNarrative}<br>${assessmentNarrative}<br>${chiefComplaintNarrative}<br>${painNarrative}<br>${historyNarrative}<br>${detailedAssessmentNarrative}`;
        document.getElementById('narrativeOutput').innerHTML = fullNarrative;
    }

    // Attach event listener to the Generate Narrative button
    document.getElementById('generateButton').addEventListener('click', generateNarrative);

    // Function to reset the form and clear the narrative
    function resetForm() {
        document.getElementById('inputForm').reset();
        document.getElementById('narrativeOutput').innerHTML = '';
        document.getElementById('painFields').style.display = 'none'; // Ensure pain fields are hidden on reset
        document.getElementById('painCheck').checked = false;
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
