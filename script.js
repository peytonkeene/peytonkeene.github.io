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

    // Attach the toggle function to the checkbox change event
    document.getElementById('painCheck').addEventListener('change', togglePainFields);

    // Function to generate the narrative based on the form inputs
    function generateNarrative() {
        // Dispatch section
        const emergenceCare = document.getElementById('emergenceCare').value;
        const responseType = document.getElementById('responseType').value;
        const dispatchDescription = document.getElementById('dispatchDescription').value;
        const dispatchNarrative = `EC ${emergenceCare} was dispatched and responded ${responseType} to the above address for a: ${dispatchDescription}.`;

        // Assessment section
        const patientPosition = document.getElementById('patientPosition').value;
        const additionalInfo = document.getElementById('additionalInfo').value;
        const orientationLevel = document.getElementById('orientationLevel').value;
        const gcs = document.getElementById('gcs').value;
        const patientAppearance = document.getElementById('patientAppearance').value;
        const assessmentNarrative = `Upon EMS arrival, the patient was found to be ${patientPosition} ${additionalInfo}. The patient is alert and oriented x${orientationLevel}. The patient's GCS is ${gcs}. The patient appears ${patientAppearance}.`;

        // Chief Complaint section
        const chiefComplaint = document.getElementById('chiefComplaint').value;
        const chiefComplaintNarrative = `Chief Complaint: ${chiefComplaint}`;

        // Pain section (conditional based on checkbox)
        var painNarrative = '';
        if (document.getElementById('painCheck').checked) {
            const painOnset = document.getElementById('painOnset').value;
            const provocationWhat = document.getElementById('provocationWhat').value;
            const provocationEffect = document.getElementById('provocationEffect').value;
            const painQuality = document.getElementById('painQuality').value;
            const painRadiation = document.getElementById('painRadiation').value;
            const painSeverity = document.getElementById('painSeverity').value;
            const painDuration = document.getElementById('painDuration').value;
            const painTimeUnit = document.getElementById('painTimeUnit').value;

            painNarrative = `Pain Assessment: Onset is ${painOnset}. Provocation: ${provocationWhat} makes the pain ${provocationEffect}. Quality: ${painQuality}. Radiation: ${painRadiation}. Severity is ${painSeverity}. Time: ${painDuration} ${painTimeUnit}.`;
        }

        // Combine narratives
        const fullNarrative = `${dispatchNarrative} ${assessmentNarrative} ${chiefComplaintNarrative} ${painNarrative}`;

        // Display the combined narrative in the output div
        document.getElementById('narrativeOutput').textContent = fullNarrative;
    }

    // Attach the generateNarrative function to the button click event
    document.getElementById('inputForm').addEventListener('submit', function(event) {
        event.preventDefault();  // Prevent form submission to server
        generateNarrative();
    });
});
