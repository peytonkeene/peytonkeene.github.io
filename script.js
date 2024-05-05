document.addEventListener("DOMContentLoaded", function() {
    const painCheck = document.getElementById("painCheck");
    const transportToggle = document.getElementById("transportToggle");
    const refusalToggle = document.getElementById("refusalToggle");
    const generateButton = document.getElementById("generateButton");
    const resetButton = document.getElementById("resetButton");
    const copyButton = document.getElementById("copyButton");

    // Function to toggle visibility of specific sections based on checkbox states
    function toggleVisibility(elementId, condition) {
        const element = document.getElementById(elementId);
        element.style.display = condition ? "block" : "none";
    }

    // Toggle functions for different sections
    function togglePainFields() {
        toggleVisibility("painFields", painCheck.checked);
        generateNarrative();
    }

    function toggleTransportFields() {
        toggleVisibility("transportDetails", transportToggle.checked);
        generateNarrative();
    }

    function toggleRefusalFields() {
        toggleVisibility("refusalDetails", refusalToggle.checked);
        generateNarrative();
    }

    // Narrative generation for all sections
    function generateNarrative() {
        let narrative = createDispatchNarrative() +
                        createArrivalNarrative() +
                        createChiefComplaintNarrative() +
                        (painCheck.checked ? createPainNarrative() : '') +
                        createHistoryNarrative() +
                        createAssessmentNarrative() +
                        (transportToggle.checked ? createTransportNarrative() : '') +
                        createTreatmentNarrative() +
                        (refusalToggle.checked ? createRefusalNarrative() : '');
        updateNarrative(narrative);
    }

    // Reset, copy, and update functions
    function resetForm() {
        document.getElementById("inputForm").reset();
        updateNarrative('');
    }

    function copyToClipboard() {
        const narrativeBox = document.getElementById("narrative");
        narrativeBox.select();
        document.execCommand("copy");
    }

    function updateNarrative(narrative) {
        const narrativeBox = document.getElementById("narrative");
        narrativeBox.value = narrative;
    }

    // Define narrative creation functions for each section
    function createDispatchNarrative() {
        const ecUnit = document.getElementById("emergenceCare").value;
        const responseType = document.getElementById("responseType").options[document.getElementById("responseType").selectedIndex].text;
        const dispatchDescription = document.getElementById("dispatchDescription").value;
        return `Dispatch: EC Unit ${ecUnit} was dispatched and responded ${responseType.toLowerCase()} to the above location for a ${dispatchDescription}.\n\n`;
    }

    function createArrivalNarrative() {
        const patientPosition = document.getElementById("patientPosition").options[document.getElementById("patientPosition").selectedIndex].text;
        const locationFound = document.getElementById("locationFound").value.trim();
        const orientationLevel = document.getElementById("orientationLevel").options[document.getElementById("orientationLevel").selectedIndex].text;
        const gcs = document.getElementById("gcs").options[document.getElementById("gcs").selectedIndex].text;
        const patientAppearance = document.getElementById("patientAppearance").options[document.getElementById("patientAppearance").selectedIndex].text;
        return `Arrival: Upon EMS arrival, the patient was found ${patientPosition} at ${locationFound}. The patient is alert and oriented x${orientationLevel} with a GCS of ${gcs}. The patient appears ${patientAppearance}.\n\n`;
    }

    function createChiefComplaintNarrative() {
        return `Chief Complaint: ${document.getElementById("chiefComplaint").value}.\n\n`;
    }

    function createPainNarrative() {
        return `Pain: Onset: ${document.getElementById("painOnset").value}, Provocation: ${document.getElementById("provocationWhat").value} makes the pain ${document.getElementById("provocationEffect").value}, Quality: ${document.getElementById("painQuality").value}, Radiation: ${document.getElementById("painRadiation").value}, Severity: ${document.getElementById("painSeverity").value}, Duration: ${document.getElementById("painDuration").value} ${document.getElementById("painTimeUnit").value}.\n\n`;
    }

    function createHistoryNarrative() {
        const pastHistory = document.getElementById("pastHistory").value.trim();
        return `History: The patient's history, medication, and allergies are noted in the patient demographic tab. The patient's past pertinent history is: ${pastHistory ? pastHistory : "No additional history provided."}\n\n`;
    }

    function createAssessmentNarrative() {
        const patientOrientation = document.getElementById("patientOrientation").options[document.getElementById("patientOrientation").selectedIndex].text;
        const patientAirway = document.getElementById("patientAirway").options[document.getElementById("patientAirway").selectedIndex].text;
        const patientBreathing = document.getElementById("patientBreathing").options[document.getElementById("patientBreathing").selectedIndex].text;
        const patientCirculation = document.getElementById("patientCirculation").options[document.getElementById("patientCirculation").selectedIndex].text;
        const skinConditions = Array.from(document.querySelectorAll('input[name="skinCondition"]:checked')).map(checkbox => checkbox.value).join(", ");
        return `Assessment: The full assessment is noted in the assessment tab of this PCR. The patient is ${patientOrientation}. The patient's airway is ${patientAirway}. The patient's breathing is ${patientBreathing}. The patient's circulation is ${patientCirculation}. The patient's skin is ${skinConditions}.\n\n`;
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

        return `Transport: The patient was transferred to ${transportDestinationText} via ${transportMethodText} and secured. The patient was transported ${transportStatusText} to ${transportDestination2Text}. The patient's status ${patientStatusText}. Upon arrival at destination, the patient was transferred to ${destinationBedText} via ${destinationTransferMethodText}. EMS then provided report and obtained signatures. The patient's care was transferred to ${careTransferText}.\n\n`;
    }

    function createRefusalNarrative() {
        const refuser = document.getElementById("refuser").options[document.getElementById("refuser").selectedIndex].text;
        return `Refusal: The ${refuser} has refused to be transported to the hospital for further evaluation and care. The ${refuser} is oriented, clear of mind, and has the capacity to understand the presented information. The ${refuser} has verbalized full understanding of their symptoms and understands that forgoing further evaluation and/or treatment could pose a significant medical risk to the patient's life. The ${refuser} has verbalized that they understand our treatment plan, including interventions and transport destinations, and does not want these interventions currently. Furthermore, the ${refuser} acknowledges that forgoing this treatment could lead to worsening of condition up to and including death. The ${refuser} understands that they are free to call 911 should the condition worsen, or they later decide that they wish to be transported to the Emergency Department for further evaluation and intervention. The ${refuser} acknowledged and assumed risks and signed the EMS Refusal Form.\n\n`;
    }

    // Attach event listeners
    painCheck.addEventListener("change", togglePainFields);
    transportToggle.addEventListener("change", toggleTransportFields);
    refusalToggle.addEventListener("change", toggleRefusalFields);
    generateButton.addEventListener("click", generateNarrative);
    resetButton.addEventListener("click", resetForm);
    copyButton.addEventListener("click", copyToClipboard);
});
