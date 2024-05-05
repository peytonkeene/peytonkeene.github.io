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

    // Function to generate narrative
    function generateNarrative() {
        console.log("Generating narrative");
        var narrative = "";

        // Dispatch section
        narrative += "Dispatch:\n";
        narrative += "EC " + document.getElementById("emergenceCare").value;
        narrative += " was dispatched and responded " + document.getElementById("responseType").options[document.getElementById("responseType").selectedIndex].textContent;
        narrative += " to the above address for a: " + document.getElementById("dispatchDescription").value + ".\n\n";

        // Arrival section
        narrative += "Arrival:\n";
        narrative += "Upon EMS arrival, the patient was found to be " + document.getElementById("patientPosition").value;
        narrative += ". The patient is alert and oriented x " + document.getElementById("orientationLevel").value;
        narrative += ". The patient's GCS is " + document.getElementById("gcs").value;
        narrative += ". The patient appears " + document.getElementById("patientAppearance").value + ".\n\n";

        // Chief Complaint section
        narrative += "Chief Complaint:\n";
        narrative += document.getElementById("chiefComplaint").value + ".\n\n";

        // Pain section
        if (document.getElementById("painCheck").checked) {
            narrative += "Pain:\n";
            narrative += "Onset: " + document.getElementById("painOnset").value;
            narrative += ", Provocation: " + document.getElementById("provocationWhat").value;
            narrative += " makes the pain " + document.getElementById("provocationEffect").value;
            narrative += ", Quality: " + document.getElementById("painQuality").value;
            narrative += ", Radiation: " + document.getElementById("painRadiation").value;
            narrative += ", Severity: " + document.getElementById("painSeverity").value;
            narrative += ", Time: " + document.getElementById("painDuration").value + " " + document.getElementById("painTimeUnit").value + ".\n\n";
        }

        // History section
        narrative += "History:\n";
        narrative += document.getElementById("pastHistory").value + ".\n\n";

        // Assessment section
        narrative += "Assessment:\n";
        narrative += "Please see the assessment tab for additional details. The patient is ";
        narrative += document.getElementById("patientOrientation").value;
        narrative += ". The patient's airway is ";
        narrative += document.getElementById("patientAirway").value;
        narrative += ". The patient's breathing is ";
        narrative += document.getElementById("patientBreathing").value;
        narrative += ". The patient's circulation is ";
        narrative += document.getElementById("patientCirculation").value;
        narrative += ". Skin conditions: ";
        var skinConditions = document.getElementsByName("skinCondition");
        var selectedSkinConditions = Array.from(skinConditions).filter(condition => condition.checked).map(condition => condition.value);
        narrative += selectedSkinConditions.join(", ") || "None selected";
        narrative += ".\n\n================================================================\n\n";  // Separator line

        // Transport section
        narrative += "Transport:\n";
        if (document.getElementById("transportToggle").checked) {
            narrative += "The patient was transferred to " + document.getElementById("transportDestination").options[document.getElementById("transportDestination").selectedIndex].textContent;
            narrative += " via " + document.getElementById("transportMethod").options[document.getElementById("transportMethod").selectedIndex].textContent;
            narrative += ". The patient was transported " + document.getElementById("transportStatus").options[document.getElementById("transportStatus").selectedIndex].textContent;
            narrative += " to " + document.getElementById("transportDestination2").options[document.getElementById("transportDestination2").selectedIndex].textContent;
            narrative += ". The patient's status " + document.getElementById("patientStatus").options[document.getElementById("patientStatus").selectedIndex].textContent;
            narrative += ". The patient's vitals were obtained and the patient was reassessed as noted. Upon arrival at the destination, the patient was transferred from the stretcher to the ";
            narrative += document.getElementById("destinationBed").options[document.getElementById("destinationBed").selectedIndex].textContent;
            narrative += " via " + document.getElementById("destinationTransferMethod").options[document.getElementById("destinationTransferMethod").selectedIndex].textContent;
            narrative += " and secured. EMS provided report and obtained signatures. The patient's care was transferred to ";
            narrative += document.getElementById("careTransfer").options[document.getElementById("careTransfer").selectedIndex].textContent;
            narrative += ".\n\n";
        } else {
            narrative += "The patient refused transportation, please see the refusal section.\n\n";
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
    document.getElementById("transportToggle").addEventListener("change", toggleTransportFields);

    // Initialize pain fields visibility
    togglePainFields();
    // Initialize transport fields visibility
    toggleTransportFields();
});
