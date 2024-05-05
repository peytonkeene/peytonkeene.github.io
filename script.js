document.addEventListener("DOMContentLoaded", function() {
    // Function to toggle visibility of pain fields based on checkbox
    function togglePainFields() {
        var painFields = document.getElementById("painFields");
        if (document.getElementById("painCheck").checked) {
            painFields.style.display = "block";
        } else {
            painFields.style.display = "none";
        }
    }

    // Function to toggle visibility of transport fields based on checkbox
    function toggleTransportFields() {
        var transportFields = document.getElementById("transportDetails");
        if (document.getElementById("transportToggle").checked) {
            transportFields.style.display = "block";
        } else {
            transportFields.style.display = "none";
        }
    }

    // Function to generate the narrative based on user input
    function generateNarrative() {
        var narrative = "";

        // Dispatch section
        narrative += "Dispatch: EC " + document.getElementById("emergenceCare").value;
        narrative += " was dispatched and responded " + document.getElementById("responseType").value;
        narrative += " to the above address for a: " + document.getElementById("dispatchDescription").value + ".\n\n";

        // Arrival section
        narrative += "Arrival: Upon EMS arrival, the patient was found ";
        narrative += document.getElementById("patientPosition").value + " at " + document.getElementById("locationFound").value;
        narrative += ". The patient is alert and oriented x " + document.getElementById("orientationLevel").value;
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
        narrative += "Assessment: The patient is " + document.getElementById("patientOrientation").value;
        narrative += ", airway " + document.getElementById("patientAirway").value;
        narrative += ", breathing " + document.getElementById("patientBreathing").value;
        narrative += ", circulation " + document.getElementById("patientCirculation").value;
        narrative += ". Skin conditions: ";
        var skinConditions = document.getElementsByName("skinCondition");
        var selectedSkinConditions = Array.from(skinConditions).filter(condition => condition.checked).map(condition => condition.value);
        narrative += selectedSkinConditions.join(", ") || "None selected";
        narrative += ".\n\n";

        // Transport section
        if (document.getElementById("transportToggle").checked) {
            narrative += "Transport: The patient was transferred to " + document.getElementById("transportDestination").value;
            narrative += " via " + document.getElementById("transportMethod").value;
            narrative += " and transported " + document.getElementById("transportStatus").value;
            narrative += ".\n\n";
        }

        // Update the textarea with the generated narrative
        document.getElementById("narrative").value = narrative;
    }

    // Function to reset the form
    function resetForm() {
        document.getElementById("inputForm").reset();
        togglePainFields();  // Ensure visibility is updated
        toggleTransportFields();  // Ensure visibility is updated
    }

    // Function to copy the generated narrative to clipboard
    function copyToClipboard() {
        var narrativeText = document.getElementById("narrative");
        narrativeText.select();
        narrativeText.setSelectionRange(0, 99999); // For mobile devices
        document.execCommand("copy");
        alert("Narrative copied to clipboard!");
    }

    // Event listeners
    document.getElementById("generateButton").addEventListener("click", generateNarrative);
    document.getElementById("resetButton").addEventListener("click", resetForm);
    document.getElementById("copyButton").addEventListener("click", copyToClipboard);
    document.getElementById("painCheck").addEventListener("change", togglePainFields);
    document.getElementById("transportCheck").addEventListener("change", toggleTransportFields);

    // Initialize pain and transport fields visibility
    togglePainFields();
    toggleTransportFields();
});
