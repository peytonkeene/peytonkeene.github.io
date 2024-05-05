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

    // Function to toggle transport details section visibility
function toggleTransportFields() {
    var transportDetails = document.getElementById("transportDetails");
    var transportToggle = document.getElementById("transportToggle");

    if (transportDetails && transportToggle) {
        // Toggle visibility based on checkbox state
        transportDetails.style.display = transportToggle.checked ? "block" : "none";
    } else {
        console.error("Transport details section or toggle checkbox not found.");
    }
}
    
    function generateNarrative() {
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
        narrative += document.getElementById("historyDetails").value + "\n\n";

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
        if (document.getElementById("transportToggle").checked) {
            narrative += "\n\nTransport: ";
            var destination = document.getElementById("transportDestination").options[document.getElementById("transportDestination").selectedIndex].text;
            var method = document.getElementById("transportMethod").options[document.getElementById("transportMethod").selectedIndex].text;
            var status = document.getElementById("transportStatus").options[document.getElementById("transportStatus").selectedIndex].text;
            var destination2 = document.getElementById("transportDestination2").options[document.getElementById("transportDestination2").selectedIndex].text;
            var patientStatus = document.getElementById("patientStatus").options[document.getElementById("patientStatus").selectedIndex].text;
            var destinationBed = document.getElementById("destinationBed").options[document.getElementById("destinationBed").selectedIndex].text;
            var transferMethod = document.getElementById("destinationTransferMethod").options[document.getElementById("destinationTransferMethod").selectedIndex].text;
            var careTransfer = document.getElementById("careTransfer").options[document.getElementById("careTransfer").selectedIndex].text;
            narrative += "The patient was transferred to " + destination + " via " + method + ". ";
            narrative += "The patient was transported " + status + " to " + destination2 + ". ";
            narrative += "The patient's status " + patientStatus + ". ";
            narrative += "Upon arrival at the destination, the patient was transferred from the stretcher to " + destinationBed + " via " + transferMethod + " and secured. ";
            narrative += "EMS provided report and obtained signatures. ";
            narrative += "The patient's care was transferred to " + careTransfer + ".";
        } else {
            narrative += "\n\nTransport: The patient refused transportation, please see the refusal section.";
        }

        // Update the text area with the generated narrative
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
    document.getElementById("transportToggle").addEventListener("change",

    // Call togglePainFields and toggleTransportFields to ensure initial visibility matches checkbox state
    togglePainFields();
    toggleTransportFields();
    
// Add event listener to the checkbox for toggling transport details
document.addEventListener("DOMContentLoaded", function() {
    var transportToggle = document.getElementById("transportToggle");
    if (transportToggle) {
        transportToggle.addEventListener("change", toggleTransportFields);
    } else {
        console.error("Transport toggle checkbox not found.");
    }
});

