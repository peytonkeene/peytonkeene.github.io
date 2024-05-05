document.addEventListener("DOMContentLoaded", function() {
    // Function to toggle visibility of pain fields based on checkbox
    function togglePainFields() {
        var painFields = document.getElementById("painFields");
        if (painFields) {
            painFields.style.display = document.getElementById("painCheck").checked ? "block" : "none";
        } else {
            console.error("Pain fields section not found.");
        }
        generateNarrative();
    }

    // Function to toggle visibility of transport fields based on checkbox
    function toggleTransportFields() {
        var transportDetails = document.getElementById("transportDetails");
        if (transportDetails) {
            transportDetails.style.display = document.getElementById("transportToggle").checked ? "block" : "none";
        } else {
            console.error("Transport details section not found.");
        }
        generateNarrative();
    }

    // Function to generate the narrative
    function generateNarrative() {
        var narrative = "";

        // Dispatch section
        narrative += "Dispatch: EC " + (document.getElementById("emergenceCare").value || '');
        narrative += " was dispatched and responded " + (document.getElementById("responseType").value || '');
        narrative += " to the above address for a: " + (document.getElementById("dispatchDescription").value || '') + ".\n\n";

        // Arrival section
        narrative += "Arrival: Upon EMS arrival, the patient was found to be " + (document.getElementById("patientPosition").value || '');
        narrative += " at " + (document.getElementById("locationFound").value || '');
        narrative += ". The patient is alert and oriented x" + (document.getElementById("orientationLevel").value || '');
        narrative += ". The patient's GCS is " + (document.getElementById("gcs").value || '');
        narrative += ". The patient appears " + (document.getElementById("patientAppearance").value || '') + ".\n\n";

        // Chief Complaint section
        narrative += "Chief Complaint: " + (document.getElementById("chiefComplaint").value || '') + ".\n\n";

        // Pain section
        if (document.getElementById("painCheck").checked) {
            narrative += "Pain: Onset: " + (document.getElementById("painOnset").value || '');
            narrative += ", Provocation: " + (document.getElementById("provocationWhat").value || '');
            narrative += " makes the pain " + (document.getElementById("provocationEffect").value || '');
            narrative += ", Quality: " + (document.getElementById("painQuality").value || '');
            narrative += ", Radiation: " + (document.getElementById("painRadiation").value || '');
            narrative += ", Severity: " + (document.getElementById("painSeverity").value || '');
            narrative += ", Time: " + (document.getElementById("painDuration").value || '') + " " + (document.getElementById("painTimeUnit").value || '') + ".\n\n";
        }

        // History section
        narrative += "History: " + (document.getElementById("pastHistory").value || '') + ".\n\n";

        // Assessment section
        narrative += "Assessment: Please see the assessment tab for additional details. The patient is " + (document.getElementById("patientOrientation").value || '');
        narrative += ". The patient's airway is " + (document.getElementById("patientAirway").value || '');
        narrative += ". The patient's breathing is " + (document.getElementById("patientBreathing").value || '');
        narrative += ". The patient's circulation is " + (document.getElementById("patientCirculation").value || '');
        narrative += ". The patient's skin conditions: ";
        var skinConditions = document.getElementsByName("skinCondition");
        var skinText = Array.from(skinConditions).filter(c => c.checked).map(c => c.value).join(", ");
        narrative += skinText || "None selected";
        narrative += ".\n\n";

        // Transport section
        if (document.getElementById("transportToggle").checked) {
            narrative += "Transport: The patient was transferred to " + (document.getElementById("transportDestination").value || '');
            narrative += " via " + (document.getElementById("transportMethod").value || '');
            narrative += ". The patient was transported " + (document.getElementById("transportStatus").value || '');
            narrative += " to " + (document.getElementById("transportDestination2").value || '');
            narrative += ". The patient's status " + (document.getElementById("patientStatus").value || '');
            narrative += ". Upon arrival at the destination, the patient was transferred from the stretcher to " + (document.getElementById("destinationBed").value || '');
            narrative += "Transport: The patient was transferred to " + (document.getElementById("transportDestination").value || '');
            narrative += " via " + (document.getElementById("transportMethod").value || '');
            narrative += ". The patient was transported " + (document.getElementById("transportStatus").value || '');
            narrative += " to " + (document.getElementById("transportDestination2").value || '');
            narrative += ". The patient's status " + (document.getElementById("patientStatus").value || '');
            narrative += ". Upon arrival at the destination, the patient was transferred from the stretcher to " + (document.getElementById("destinationBed").value || '') + ".";
        }

        // Treatment section
        narrative += "\n\nTreatment: ";
        var treatmentCheckboxes = document.querySelectorAll('input[name="treatment"]:checked');
        if (treatmentCheckboxes.length > 0) {
            var treatments = [];
            treatmentCheckboxes.forEach(function(checkbox) {
                treatments.push(checkbox.parentElement.textContent.trim());
            });
            narrative += treatments.join(", ");
        } else {
            narrative += "None";
        }

        // Additional treatment section
        var additionalTreatment = document.querySelector('textarea[name="additionalTreatment"]').value.trim();
        if (additionalTreatment) {
            narrative += "\n\nAdditional Treatment: " + additionalTreatment;
        }

        // Update the narrative textarea with the generated text
        var narrativeBox = document.getElementById("narrative");
        if (narrativeBox) {
            narrativeBox.value = narrative;
        } else {
            console.error("Narrative textarea not found.");
        }
    }

    // Function to reset the form
    function resetForm() {
        var form = document.getElementById("inputForm");
        if (form) {
            form.reset();
        } else {
            console.error("Form not found.");
        }
        // Reset the narrative
        var narrativeBox = document.getElementById("narrative");
        if (narrativeBox) {
            narrativeBox.value = "";
        } else {
            console.error("Narrative textarea not found.");
        }
    }

    // Function to copy narrative to clipboard
    function copyToClipboard() {
        var narrativeBox = document.getElementById("narrative");
        if (narrativeBox) {
            narrativeBox.select();
            document.execCommand("copy");
        } else {
            console.error("Narrative textarea not found.");
        }
    }

    // Event listeners for toggling sections and generating narrative
    document.getElementById("painCheck").addEventListener("change", togglePainFields);
    document.getElementById("transportToggle").addEventListener("change", toggleTransportFields);
    document.getElementById("generateButton").addEventListener("click", generateNarrative);
    document.getElementById("resetButton").addEventListener("click", resetForm);
    document.getElementById("copyButton").addEventListener("click", copyToClipboard);
});
