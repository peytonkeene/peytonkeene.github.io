document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM content loaded");

    // Function to toggle visibility of pain fields based on checkbox
    function togglePainFields() {
        console.log("Toggling pain fields");
        var painFields = document.getElementById("painFields");
        if (document.getElementById("painCheck").checked) {
            painFields.style.display = "block";
        } else {
            painFields.style.display = "none";
        }
    }

    // Function to toggle visibility of transport fields based on checkbox
    function toggleTransportFields() {
        console.log("Toggling transport fields");
        var transportFields = document.getElementById("transportDetails");
        if (document.getElementById("transportToggle").checked) {
            transportFields.style.display = "block";
        } else {
            transportFields.style.display = "none";
        }
    }

    function generateNarrative() {
        console.log("Generating narrative");
        var narrative = "";

        // Dispatch section
        narrative += "Dispatch: ";
        narrative += "EC " + document.getElementById("emergenceCare").value;
        narrative += " was dispatched and responded " + document.getElementById("responseType").value;
        narrative += " to the above address for a: " + document.getElementById("dispatchDescription").value + "\n\n";

        // Arrival section
        narrative += "Arrival: ";
        narrative += "Upon EMS arrival, the patient was found to be " + document.getElementById("patientPosition").value;
        narrative += ". The patient is alert and oriented x " + document.getElementById("orientationLevel").value;
        narrative += ". The patient's GCS is " + document.getElementById("gcs").value;
        narrative += ". The patient appears " + document.getElementById("patientAppearance").value + "\n\n";

        // Chief Complaint section
        narrative += "Chief Complaint: ";
        narrative += document.getElementById("chiefComplaint").value + "\n\n";

        // Pain section
        if (document.getElementById("painCheck").checked) {
            narrative += "Pain: ";
            narrative += "Onset: " + document.getElementById("painOnset").value;
            narrative += ". Provocation: " + document.getElementById("provocationWhat").value;
            narrative += " makes the pain " + document.getElementById("provocationEffect").value;
            narrative += ". Quality: " + document.getElementById("painQuality").value;
            narrative += ". Radiation: " + document.getElementById("painRadiation").value;
            narrative += ". Severity: " + document.getElementById("painSeverity").value;
            narrative += ". Time: " + document.getElementById("painDuration").value + " " + document.getElementById("painTimeUnit").value + "\n\n";
        }

        // History section
        narrative += "History: ";
        narrative += document.getElementById("pastHistory").value + "\n\n";

        // Assessment section
        narrative += "Assessment: ";
        narrative += "Please see the assessment tab for additional details. The patient is ";
        narrative += document.getElementById("patientOrientation").value;
        narrative += ". The patient's airway is ";
        narrative += document.getElementById("patientAirway").value;
        narrative += ". The patient's breathing is ";
        narrative += document.getElementById("patientBreathing").value;
        narrative += ". The patient's circulation is ";
        narrative += document.getElementById("patientCirculation").value;
        narrative += ". The patient's skin is: ";
        var skinConditions = document.getElementsByName("skinCondition");
        var skinConditionsArray = Array.from(skinConditions);
        var selectedSkinConditions = skinConditionsArray.filter(condition => condition.checked).map(condition => condition.value);
        narrative += selectedSkinConditions.join(", ") || "None selected";

         // Transport section
    narrative += "\n\nTransport: ";
    if (document.getElementById("transportToggle").checked) {
        narrative += "The patient was transferred to ";
        narrative += document.getElementById("transportDestination").value;
        narrative += " via ";
        narrative += document.getElementById("transportMethod").value;
        narrative += ". The patient was transported ";
        narrative += document.getElementById("transportStatus").value;
        narrative += " to ";
        narrative += document.getElementById("transportDestination2").value;
        narrative += ". The patient's status ";
        narrative += document.getElementById("patientStatus").value;
        narrative += ". The patient's vitals were obtained and the patient was reassessed as noted. Upon arrival at the destination, the patient was transferred from the stretcher to the ";
        narrative += document.getElementById("destinationBed").value;
        narrative += " via ";
        narrative += document.getElementById("destinationTransferMethod").value;
        narrative += " and secured. EMS provided report and obtained signatures. The patient's care was transferred to ";
        narrative += document.getElementById("careTransfer").value;
    } else {
        narrative += "The patient was not transported, see refusal section.";
    }
        // Update the text area with the generated narrative
        document.getElementById("narrative").value = narrative;
    }

    // Function to reset the form
    function resetForm() {
        console.log("Resetting form");
        document.getElementById("inputForm").reset();
        togglePainFields();  // Ensure visibility is updated
        toggleTransportFields();  // Ensure visibility is updated
    }

    // Function to copy the generated narrative to clipboard
    function copyToClipboard() {
        console.log("Copying narrative to clipboard");
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
    document.getElementById("transportToggle").addEventListener("change", toggleTransportFields);

    // Initialize pain fields visibility
    togglePainFields();
    // Initialize transport fields visibility
    toggleTransportFields();
});
