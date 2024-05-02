document.addEventListener('DOMContentLoaded', function () {
    // Function to toggle the visibility of the Pain section
    function togglePainFields() {
        var painSection = document.getElementById('painFields');
        if (painSection) {
            if (document.getElementById('painCheck').checked) {
                painSection.style.display = 'block';
            } else {
                painSection.style.display = 'none';
            }
        } else {
            console.log("Pain section not found.");
        }
    }

    // Function to generate the narrative based on the form inputs
    function generateNarrative() {
        console.log("Generating narrative...");
        const emergenceCare = document.getElementById('emergenceCare');
        const responseType = document.getElementById('responseType');
        const dispatchDescription = document.getElementById('dispatchDescription');

        if (emergenceCare && responseType && dispatchDescription) {
            const dispatchNarrative = `<h2>Dispatch</h2> EC ${emergenceCare.value} was dispatched and responded ${responseType.value} to the above address for a: ${dispatchDescription.value}.`;

            const patientPosition = document.getElementById('patientPosition');
            const additionalInfo = document.getElementById('additionalInfo');
            const orientationLevel = document.getElementById('orientationLevel');
            const gcs = document.getElementById('gcs');
            const patientAppearance = document.getElementById('patientAppearance');
            const chiefComplaint = document.getElementById('chiefComplaint');
            const narrativeOutput = document.getElementById('narrativeOutput');

            if (patientPosition && additionalInfo && orientationLevel && gcs && patientAppearance && chiefComplaint && narrativeOutput) {
                const assessmentNarrative = `<h2>Assessment</h2> Upon EMS arrival, the patient was found to be ${patientPosition.value} ${additionalInfo.value}. The patient is alert and oriented x${orientationLevel.value}. The patient's GCS is ${gcs.value}. The patient appears ${patientAppearance.value}.`;
                const chiefComplaintNarrative = `<h2>Chief Complaint</h2> Chief Complaint: ${chiefComplaint.value}`;
                
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

                    painNarrative = `<h2>Pain</h2> Pain Assessment: Onset is ${painOnset}. Provocation: ${provocationWhat} makes the pain ${provocationEffect}. Quality: ${painQuality}. Radiation: ${painRadiation}. Severity is ${painSeverity}. Time: ${painDuration} ${painTimeUnit}.`;
                }

                // Combine narratives
                const fullNarrative = `${dispatchNarrative} ${assessmentNarrative} ${chiefComplaintNarrative} ${painNarrative}`;
                narrativeOutput.innerHTML = fullNarrative;
            } else {
                console.log("One or more assessment elements are missing.");
            }
        } else {
            console.log("One or more dispatch elements are missing.");
        }
    }

    // Attach event listeners
    var painCheck = document.getElementById('painCheck');
    if (painCheck) {
        painCheck.addEventListener('change', togglePainFields);
    } else {
        console.log("Pain check checkbox not found.");
    }

    var generateButton = document.getElementById('generateButton');
    if (generateButton) {
        generateButton.addEventListener('click', generateNarrative);
    } else {
        console.log("Generate button not found.");
    }
});
