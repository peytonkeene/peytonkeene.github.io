document.addEventListener("DOMContentLoaded", function() {
    const painCheck = document.getElementById("painCheck");
    const transportToggle = document.getElementById("transportToggle");
    const generateButton = document.getElementById("generateButton");
    const resetButton = document.getElementById("resetButton");
    const copyButton = document.getElementById("copyButton");

    // Toggle visibility based on condition
    function toggleVisibility(elementId, condition) {
        const element = document.getElementById(elementId);
        if (element) {
            element.style.display = condition ? "block" : "none";
        } else {
            console.error(`${elementId} not found.`);
        }
    }

    // Toggles the visibility of the pain fields
    function togglePainFields() {
        toggleVisibility("painFields", painCheck.checked);
        generateNarrative();
    }

    // Toggles the visibility of the transport details
    function toggleTransportFields() {
        toggleVisibility("transportDetails", transportToggle.checked);
        generateNarrative();
    }

    // Generates the EMS narrative based on the current state of the form
    function generateNarrative() {
        let narrative = createDispatchNarrative() +
                        createArrivalNarrative() +
                        createChiefComplaintNarrative() +
                        (painCheck.checked ? createPainNarrative() : '') +
                        createHistoryNarrative() +
                        createAssessmentNarrative() +
                        (transportToggle.checked ? createTransportNarrative() : '') +
                        createTreatmentNarrative();

        updateNarrative(narrative);
    }

    // Resets the form and the narrative
    function resetForm() {
        document.getElementById("inputForm").reset();
        updateNarrative('');
    }

    // Copies the narrative to the clipboard
    function copyToClipboard() {
        const narrativeBox = document.getElementById("narrative");
        if (narrativeBox) {
            narrativeBox.select();
            document.execCommand("copy");
        } else {
            console.error("Narrative textarea not found.");
        }
    }

    // Updates the narrative text area
    function updateNarrative(narrative) {
        const narrativeBox = document.getElementById("narrative");
        if (narrativeBox) {
            narrativeBox.value = narrative;
        } else {
            console.error("Narrative textarea not found.");
        }
    }

    // Define narrative creation functions for each section
    function createDispatchNarrative() {
        return `Dispatch: EC ${document.getElementById("emergenceCare").value} was dispatched and responded ${document.getElementById("responseType").value} for: ${document.getElementById("dispatchDescription").value}.\n\n`;
    }

    function createArrivalNarrative() {
        return `Arrival: Upon EMS arrival, the patient was found ${document.getElementById("patientPosition").value} at ${document.getElementById("locationFound").value}. Orientation Level: ${document.getElementById("orientationLevel").value}. GCS: ${document.getElementById("gcs").value}. Appearance: ${document.getElementById("patientAppearance").value}.\n\n`;
    }

    function createChiefComplaintNarrative() {
        return `Chief Complaint: ${document.getElementById("chiefComplaint").value}.\n\n`;
    }

    function createPainNarrative() {
        return `Pain: Onset: ${document.getElementById("painOnset").value}, Provocation: ${document.getElementById("provocationWhat").value} makes the pain ${document.getElementById("provocationEffect").value}, Quality: ${document.getElementById("painQuality").value}, Radiation: ${document.getElementById("painRadiation").value}, Severity: ${document.getElementById("painSeverity").value}, Duration: ${document.getElementById("painDuration").value} ${document.getElementById("painTimeUnit").value}.\n\n`;
    }

    function createHistoryNarrative() {
        return `History: ${document.getElementById("pastHistory").value}.\n\n`;
    }

    function createAssessmentNarrative() {
        const skinConditions = Array.from(document.getElementsByName("skinCondition")).filter(c => c.checked).map(c => c.value).join(", ");
        return `Assessment: The patient is ${document.getElementById("patientOrientation").value}. Airway: ${document.getElementById("patientAirway").value}. Breathing: ${document.getElementById("patientBreathing").value}. Circulation: ${document.getElementById("patientCirculation").value}. Skin: ${skinConditions}.\n\n`;
    }

    function createTransportNarrative() {
        return `Transport: The patient was transported to ${document.getElementById("transportDestination2").value} via ${document.getElementById("transportMethod").value}. Status during transport: ${document.getElementById("transportStatus").value}. Upon arrival, transferred to ${document.getElementById("destinationBed").value}.\n\n`;
    }

    function createTreatmentNarrative() {
        const treatments = Array.from(document.querySelectorAll('input[name="treatment"]:checked')).map(c => c.nextElementSibling.textContent.trim()).join(", ");
        return treatments ? `Treatment: ${treatments}.\n\n` : 'Treatment: None.\n\n';
    }

    // Attach event listeners
    painCheck.addEventListener("change", togglePainFields);
    transportToggle.addEventListener("change", toggleTransportFields);
    generateButton.addEventListener("click", generateNarrative);
    resetButton.addEventListener("click", resetForm);
    copyButton.addEventListener("click", copyToClipboard);
});
