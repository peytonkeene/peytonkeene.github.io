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
    const patientOrientation = document.getElementById("patientOrientation").options[document.getElementById("patientOrientation").selectedIndex].text;
    const patientAirway = document.getElementById("patientAirway").options[document.getElementById("patientAirway").selectedIndex].text;
    const patientBreathing = document.getElementById("patientBreathing").options[document.getElementById("patientBreathing").selectedIndex].text;
    const patientCirculation = document.getElementById("patientCirculation").options[document.getElementById("patientCirculation").selectedIndex].text;

    // Collect all checked skin conditions
    const skinConditionCheckboxes = document.querySelectorAll('input[name="skinCondition"]:checked');
    let skinConditions = Array.from(skinConditionCheckboxes).map(checkbox => checkbox.value).join(", ");
    skinConditions = skinConditions || "no notable conditions";  // Provide a default if no checkboxes are checked

    return `Assessment: The full assessment is noted in the assessment tab of this PCR. The patient is ${patientOrientation}. The patient's airway is ${patientAirway}. The patient's breathing is ${patientBreathing}. The patient's circulation ${patientCirculation}. The patient's skin is ${skinConditions}.\n\n`;
}

    function createTransportNarrative() {
        const transportDestinationText = document.querySelector('#transportDestination option:checked').textContent;
        const transportMethodText = document.querySelector('#transportMethod option:checked').textContent;
        const transportStatusText = document.querySelector('#transportStatus option:checked').textContent;
        const transportDestination2Text = document.querySelector('#transportDestination2 option:checked').textContent;
        const patientStatusText = document.querySelector('#patientStatus option:checked').textContent;
        const destinationBedText = document.querySelector('#destinationBed option:checked').textContent;
        const destinationTransferMethodText = document.querySelector('#destinationTransferMethod option:checked').textContent;
        const careTransferText = document.querySelector('#careTransfer option:checked').textContent;

        return `The patient was transferred to ${transportDestinationText} via ${transportMethodText} and secured. The patient was transported ${transportStatusText} to ${transportDestination2Text}. The patient's status ${patientStatusText}. Upon arrival at destination, the patient was transferred to ${destinationBedText} via ${destinationTransferMethodText}. EMS then provided report and obtained signatures. The patient's care was transferred to ${careTransferText}.\n\n`;
    }

   function createTreatmentNarrative() {
    const treatmentCheckboxes = document.querySelectorAll('input[name="treatment"]:checked');
    let treatments = [];

    treatmentCheckboxes.forEach(checkbox => {
        if (checkbox.id === "other" && checkbox.checked) {
            // Include the text from the additionalTreatment textarea if "Other" is checked
            const additionalTreatmentText = document.querySelector('textarea[name="additionalTreatment"]').value.trim();
            if (additionalTreatmentText) {
                treatments.push("Other treatment specified: " + additionalTreatmentText);
            } else {
                treatments.push("Other treatment not listed.");
            }
        } else {
            // Use the next sibling's text content for the label description
            const labelDescription = checkbox.nextElementSibling ? checkbox.nextElementSibling.textContent.trim() : 'Unknown treatment';
            treatments.push(labelDescription);
        }
    });

    // Concatenate all treatments into a single string, separated by spaces or new lines
    return treatments.length > 0 ? `Treatment: ${treatments.join(". ")}.\n\n` : 'Treatment: None.\n\n';
}

    // Attach event listeners
    painCheck.addEventListener("change", togglePainFields);
    transportToggle.addEventListener("change", toggleTransportFields);
    generateButton.addEventListener("click", generateNarrative);
    resetButton.addEventListener("click", resetForm);
    copyButton.addEventListener("click", copyToClipboard);
});
