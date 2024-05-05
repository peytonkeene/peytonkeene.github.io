document.addEventListener("DOMContentLoaded", function() {
    // Function to toggle visibility of pain fields based on checkbox
    function togglePainFields() {
        var painFields = document.getElementById("painFields");
        painFields.style.display = document.getElementById("painCheck").checked ? "block" : "none";
        generateNarrative(); // Regenerate narrative when pain toggle changes
    }

    // Function to toggle visibility of transport fields based on checkbox
    function toggleTransportFields() {
        var transportDetails = document.getElementById("transportDetails");
        transportDetails.style.display = document.getElementById("transportToggle").checked ? "block" : "none";
        generateNarrative(); // Regenerate narrative when transport toggle changes
    }

    // Function to generate the narrative
    function generateNarrative() {
        var narrative = "";

        // Dispatch section
        narrative += "Dispatch: EC " + document.getElementById("emergenceCare").value;
        narrative += " was dispatched and responded " + document.getElementById("responseType").value;
        narrative += " to the above address for a: " + document.getElementById("dispatchDescription").value + ".\n\n";

        // Arrival section
        narrative += "Arrival: Upon EMS arrival, the patient was found to be " + document.getElementById("patientPosition").value;
        narrative += " at " + document.getElementById("locationFound").value;
        narrative += ". The patient is alert and oriented x" + document.getElementById("orientationLevel").value;
        narrative += ". The patient's GCS is " + document.getElementById("gcs").value;
        narrative += ". The patient appears " + document.getElementById("patientAppearance").value + ".\n\n";

        // Chief Complaint section
        narrative += "Chief Complaint: " + document.getElementById("chiefComplaint").value + ".\n\n";

        // Pain section
        if (document.getElementById("painCheck").checked) {
            narrative += "Pain: Onset: " + document.getElementById("painOnset").value;
            narrative += ", Provocation: " + document.getElementById("provocationWhat").value;
            narrative += " makes the pain " + document.getElementById("provocationEffect").value;
            narrative += ", Quality: " + document.getElementById("painQuality").value;
            narrative += ", Radiation: " + document.getElementById("painRadiation").value;
            narrative += ", Severity: " + document.getElementById("painSeverity").value;
            narrative += ", Time: " + document.getElementById("painDuration").value + " " + document.getElementById("painTimeUnit").value + ".\n\n";
        }

        // History section
        narrative += "History: " + document.getElementById("pastHistory").value + ".\n\n";

        // Assessment section
        narrative += "Assessment: Please see the assessment tab for additional details. The patient is ";
        narrative += document.getElementById("patientOrientation").value;
        narrative += ". The patient's airway is " + document.getElementById("patientAirway").value;
        narrative += ". The patient's breathing is " + document.getElementById("patientBreathing").value;
        narrative += ". The patient's circulation is " + document.getElementById("patientCirculation").value;
        narrative += ". The patient's skin conditions: ";
        var skinConditions = document.getElementsByName("skinCondition");
        var skinConditionsText = Array.from(skinConditions).filter(c => c.checked).map(c => c.value).join(", ");
        narrative += skinConditionsText || "None selected";
        narrative += ".\n\n";

        // Transport section
        if (document.getElementById("transportToggle").checked) {
            narrative += "Transport: The patient was transferred to " + document.getElementById("transportDestination").value;
            narrative += " via " + document.getElementById("transportMethod").value;
            narrative += ". The patient was transported " + document.getElementById("transportStatus").value;
            narrative += " to " + document.getElementById("transportDestination2").value;
            narrative += ". The patient's status " + document.getElementById("patientStatus").value;
            narrative += ". Upon arrival at the destination, the patient was transferred from the stretcher to " + document.getElementById("destinationBed").value;
            narrative += " via " + document.getElementById("destinationTransferMethod").value;
            narrative += " and secured. EMS provided report and obtained signatures. The patient's care was transferred to " + document.getElementById("careTransfer").value;
            narrative += ".\n\n";
        } else {
            narrative += "Transport: The patient refused transportation, please see the refusal section.\n\n";
        }

        // Update the text area with the generated narrative
        document.getElementById("narrative").value = narrative;
    }

    // Function to reset the form
    function resetForm() {
        document.getElementById("inputForm").reset();
        togglePainFields();  // Ensure visibility is updated
        toggleTransportFields();  // Ensure visibility is updated
        generateNarrative(); // Clear the narrative text
    }

    // Function to copy the generated narrative to clipboard
    function copyToClipboard() {
        var narrativeText = document.getElementById("narrative");
        narrativeText.select();  // Select the text
        narrativeText.setSelectionRange(0, 99999); // For mobile devices

        // Copy the selected text to clipboard
        document.execCommand("copy");
        alert("Narrative copied to clipboard!");
    }

    // Event listeners
    document.getElementById("generateButton").addEventListener("click", generateNarrative);
    document.getElementById("resetButton").addEventListener("click", resetForm);
    document.getElementById("copyButton").addEventListener("click", copyToClipboard);
    document.getElementById("painCheck").addEventListener("change", togglePainFields);
    document.getElementById("transportToggle").addEventListener("change", toggleTransportFields);

    // Initial toggle calls to ensure the proper display when the page loads
    togglePainFields();  // Set initial visibility for pain fields
    toggleTransportFields();  // Set initial visibility for transport details
});
