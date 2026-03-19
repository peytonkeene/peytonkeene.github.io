// Tab navigation functionality
const tabs = document.querySelectorAll('.tab');
const tabButtons = document.querySelectorAll('.tab-buttons button');

tabButtons.forEach(button => {
  button.addEventListener('click', () => {
    const tabId = button.getAttribute('data-tab');
    
    // Hide all tabs and remove active class from all buttons
    tabs.forEach(tab => tab.classList.remove('active'));
    tabButtons.forEach(btn => btn.classList.remove('active'));
    
    // Show selected tab and add active class to the button
    document.getElementById(tabId).classList.add('active');
    button.classList.add('active');
  });
});

// Toggle treatment selection for bubble options
const bubbleOptions = document.querySelectorAll('.bubble-option');
bubbleOptions.forEach(option => {
  option.addEventListener('click', function() {
    // Toggle the active class to visually indicate selection
    this.classList.toggle('active');
    
    // If the option is "Other", show or hide the additional textbox
    if (this.dataset.treatment === "Other") {
      document.getElementById('otherTextBox').style.display = this.classList.contains('active') ? 'block' : 'none';
    }
  });
});

// Toggle visibility for Transport/Refusal fields based on selection
document.getElementById('transportDecision').addEventListener('change', function() {
  const decision = this.value;
  const transportFields = document.getElementById('transportFields');
  const refusalFields = document.getElementById('refusalFields');
  
  if (decision === 'Transport') {
    transportFields.style.display = 'block';
    refusalFields.style.display = 'none';
  } else if (decision === 'Refusal') {
    transportFields.style.display = 'none';
    refusalFields.style.display = 'block';
  }
});

// Medication Popup Functions
function showMedicationPopup() {
  document.getElementById('medicationPopup').style.display = 'block';
}

function closeMedicationPopup() {
  document.getElementById('medicationPopup').style.display = 'none';
}

function addMedication() {
  // Gather input values from the medication popup
  const medName = document.getElementById('medicationName').value;
  const medDose = document.getElementById('medicationDose').value;
  const medRoute = document.getElementById('medicationRoute').value;
  const medImprovement = document.getElementById('medicationImprovement').value;
  
  // Create a medication detail string
  let medicationDetail = `Medication: ${medName}, Dose: ${medDose}, Route: ${medRoute}, Improvement: ${medImprovement}`;
  
  // Append the medication detail to the medication list (for display purposes)
  const medList = document.getElementById('medicationList');
  let medItem = document.createElement('div');
  medItem.innerText = medicationDetail;
  medList.appendChild(medItem);
  
  // Close the popup and clear the inputs
  closeMedicationPopup();
  document.getElementById('medicationName').value = '';
  document.getElementById('medicationDose').value = '';
  document.getElementById('medicationRoute').value = '';
  document.getElementById('medicationImprovement').value = '';
}

// Narrative generation function
document.getElementById('generateBtn').addEventListener('click', () => {
  // DISPATCH Tab values
  const runNumber = document.getElementById('runNumber').value;
  const callType = document.getElementById('callType').value;
  const patientAge = document.getElementById('patientAge').value;
  const patientGender = document.getElementById('patientGender').value;
  const emdComplaint = document.getElementById('emdComplaint').value;
  const dispatchLocation = document.getElementById('dispatchLocation').value;
  
  // RESPONSE Tab values
  const responseUnit = document.getElementById('responseUnit').value;
  const responseType = document.getElementById('responseType').value;
  const lightsSirenResponse = document.getElementById('lightsSirenResponse').value;
  const lightPattern = document.getElementById('lightPattern').value;
  let responders = [];
  if(document.getElementById('hcsd').checked) responders.push("Hughes County Sheriffs Department");
  if(document.getElementById('hcfd').checked) responders.push("Hughes County Fire Department");
  if(document.getElementById('hcems').checked) responders.push("Hughes County EMS");
  
  // ARRIVAL Tab values
  const patientLocationFound = document.getElementById('patientLocation').value;
  const avpu = document.getElementById('avpu').value;
  const orientation = document.getElementById('orientation').value; // First orientation field
  const gcs = document.getElementById('gcs').value;
  // Attempt to get a GCS qualifier if available (if you update the id from "orientation" to "gcsQualifier")
  const gcsQualifier = document.getElementById('gcsQualifier') ? document.getElementById('gcsQualifier').value : "";
  const airwayAssessment = document.getElementById('airwayAssessment').value;
  const breathingAssessment = document.getElementById('breathingAssessment').value;
  const circulationAssessment = document.getElementById('circulationAssessment').value;
  const primaryAssessment = document.getElementById('primaryAssessment').value;
  
  // ASSESSMENT Tab values
  const assessmentLevel = document.getElementById('assessmentLevel').value;
  const airwayReassessment = document.getElementById('airwayReassessment').value;
  const breathingReassessment = document.getElementById('breathingReassessment').value;
  const circulationReassessment = document.getElementById('circulationReassessment').value;
  const pastHistory = document.getElementById('pastHistory').value;
  const chiefComplaint = document.getElementById('chiefComplaint').value;
  const primaryImpression = document.getElementById('primaryImpression').value;
  const ecgInterpretation = document.getElementById('ecgInterpretation').value;
  const vitalSigns = document.getElementById('vitalSigns').value;
  const mobilityAssessment = document.getElementById('mobilityAssessment').value;
  const assessmentNotes = document.getElementById('assessmentNotes').value;
  const reassessTime = document.getElementById('reassessTime').value;
  const reassessmentNotes = document.getElementById('reassessmentNotes').value;
  
  // TREATMENT Tab - gather selected treatments from bubble options
  let selectedTreatments = [];
  document.querySelectorAll('#treatment .bubble-option.active').forEach(el => {
    if (el.dataset.treatment === "Other") {
      let otherText = document.getElementById('otherTreatment').value;
      if (otherText.trim() !== "") {
        selectedTreatments.push(otherText);
      }
    } else {
      selectedTreatments.push(el.dataset.treatment);
    }
  });
  
  /// TRANSPORT/REFUSAL Tab
  const transportDecision = document.getElementById('transportDecision').value;
  let transportNarrative = "";

  if (transportDecision === 'Transport') {
    // Get the values from the new IDs
    const destinationLocationValue = document.getElementById('destinationLocation').value;
    const finalDestinationValue = document.getElementById('destination').value;
    
    const movementMethod = document.getElementById('movementMethod').value;
    const movementReason = document.getElementById('movementReason').value;
    const movementDestination = document.getElementById('movementDestination').value;
    const locationReason = document.getElementById('locationReason').value;
    const transportType = document.getElementById('transportType').value;
    const transportReason = document.getElementById('transportReason').value;
    const transferMethod = document.getElementById('transferMethod').value;
    const transferReason = document.getElementById('transferReason').value;
    const transferCare = document.getElementById('transferCare').value;
    
    transportNarrative += `The patient was moved via ${movementMethod} ${movementReason} to the ${movementDestination}. `;
    transportNarrative += `The patient was transported ${transportType} due to ${transportReason}. `;
    transportNarrative += `The patient was transported to ${destinationLocationValue} due to ${locationReason}. `;
    transportNarrative += `The patient was transferred to ${finalDestinationValue} via ${transferMethod} ${transferReason}. `;
    transportNarrative += `The patient's care was transferred to a ${transferCare} with proper signatures obtained.`;
  } else if (transportDecision === 'Refusal') {
    const refusalParty = document.getElementById('refusalParty').value;
    const refusalReason = document.getElementById('refusalReason').value;
    transportNarrative += `<strong>Decision:</strong> Refusal. `;
    transportNarrative += `<strong>Refusal Party:</strong> ${refusalParty}. `;
    transportNarrative += `<strong>Refusal Reason:</strong> ${refusalReason}.`;
  }
  
  // Build the full narrative HTML
  let narrative = "";
  //Run Number
  
  // Dispatch Section
  narrative += "<h2>DISPATCH</h2>";
  narrative += `<p>
    <strong>Run Number:</strong> ${runNumber}\n
    EMS was dispatched to a ${callType} for a ${patientAge} year old ${patientGender} with a complaint of ${emdComplaint} at ${dispatchLocation}.
  </p>`;
  
  // Response Section
  narrative += "<h2>RESPONSE</h2>";
  narrative += `<p>
    ${responseUnit} responded ${responseType} ${lightsSirenResponse} ${lightPattern}. ${responders.join(", ")} also responded alongside ${responseUnit}
  </p>`;
  
  // Arrival Section
  narrative += "<h2>ARRIVAL</h2>";
  narrative += `<p>
    Upon EMS arrival, the patient was found ${patientLocationFound}. The patient was ${avpu} and oriented ${orientation}. The patient had a GCS of ${gcs} which is ${gcsQualifier}. The patient's airway was ${airwayAssessment}. The patient's breathing was ${breathingAssessment}. The patient's circulation was noted to be ${circulationAssessment}. The patient had ${primaryAssessment}.
  </p>`;
  
  // Assessment Section
  narrative += "<h2>ASSESSMENT</h2>";
  narrative += `<p>
    EMS provided a(n) ${assessmentLevel}. Upon further assessment the patient was found to have a ${airwayReassessment}. The patient's breathing was found to be ${breathingReassessment}. The patient's circulation was found to be ${circulationReassessment}. The patient's full history is noted in the patient section of this EPCR. The patient's past pertinent medical history includes ${pastHistory}. The patient had a chief complaint of "${chiefComplaint}". The provider's had a differential diagnosis of ${primaryImpression}.${ecgInterpretation}. The patient's full vital signs are noted in the vitals section of this EPCR. The patient had ${vitalSigns}. The patient was ${mobilityAssessment}. ${assessmentNotes} The patient was ${reassessTime}. ${reassessmentNotes}.
  </p>`;
  
  // Treatment Section
  narrative += "<h2>TREATMENT</h2>";
  if (selectedTreatments.length > 0) {
    narrative += `<p>${selectedTreatments.join(" ") + ""}</p>`;
  } else {
    narrative += "<p>No treatments were provided to the patient.</p>";
  }
  
  // Transport/Refusal Section
  narrative += "<h2>TRANSPORT/REFUSAL</h2>";
  narrative += `<p>${transportNarrative}</p>`;
  
  // Output the narrative (using innerHTML so the HTML tags render)
  document.getElementById('narrativeOutput').innerHTML = narrative;
});
