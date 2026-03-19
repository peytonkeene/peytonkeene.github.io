<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>EMS Narrative Generator</title>
  <!-- Link to external CSS file -->
  <link rel="stylesheet" href="hughes_styles.css">
</head>
<body>
  <h1>Hughes County Narrative Generator</h1>
  
  <!-- Tab Buttons -->
  <div class="tab-buttons">
    <button data-tab="dispatch" class="active">DISPATCH</button>
    <button data-tab="response">RESPONSE</button>
    <button data-tab="arrival">ARRIVAL</button>
    <button data-tab="assessment">ASSESSMENT</button>
    <button data-tab="treatment">TREATMENT</button>
    <button data-tab="transport">TRANSPORT/REFUSAL</button>
    <button data-tab="generate">GENERATE</button>
  </div>
  
  <!-- DISPATCH Tab -->
  <div id="dispatch" class="tab active">
    <h2>DISPATCH</h2>
    <label for="runNumber">Run Number:</label>
    <input type="text" id="runNumber" placeholder="051MMYY###">
    
    <label for="callType">Call Type:</label>
    <select id="callType">
      <option value="911">911</option>
      <option value="emergent transfer">Emergent Transfer</option>
      <option value="non-emergent transfer">Non-Emergent Transfer</option>
    </select>
    
    <label for="patientAge">Patient Age:</label>
    <input type="number" id="patientAge" min="0" max="110" placeholder="25">
    
    <label for="patientGender">Patient Gender:</label>
    <select id="patientGender">
      <option value="male">Male</option>
      <option value="female">Female</option>
    </select>
    
    <label for="emdComplaint">EMD Complaint:</label>
    <input type="text" id="emdComplaint" placeholder="chest pain, sick person, etc">
    </select>
    
    <label for="dispatchLocation">Dispatch Location:</label>
    <input type="text" id="dispatchLocation" placeholder="private residence, hospital, etc">
    </select>
  </div>
  
  <!-- RESPONSE Tab -->
  <div id="response" class="tab">
    <h2>RESPONSE</h2>
    <label for="responseUnit">Response Unit:</label>
    <select id="responseUnit">
      <option value="EMS 1">EMS 1</option>
      <option value="EMS 2">EMS 2</option>
      <option value="EMS 3">EMS 3</option>
      <option value="EMS 4">EMS 4</option>
      <option value="EMS 5">EMS 5</option>
    </select>
    
    <label for="responseType">Response Type:</label>
    <select id="responseType">
      <option value="emergent">Emergent</option>
      <option value="non-emergent">Non-Emergent</option>
    </select>
    
    <label for="lightsSirenResponse">Lights &amp; Siren:</label>
    <select id="lightsSirenResponse">
      <option value="with lights and sirens">With Lights and Sirens</option>
      <option value="without lights and sirens">Without Lights and Sirens</option>
    </select>
    
    <label for="lightPattern">Lights &amp; Siren:</label>
    <select id="lightPattern">
      <option value="against normal light pattern">Against Normal Light Pattern</option>
      <option value="with normal light pattern">with normal light pattern</option>
    </select>
    
    <label for="additionalResponders">Additional Responders:</label>
    <div id="additionalResponders">
      <input type="checkbox" id="hcsd" name="additionalResponders[]" value="Hughes County Sheriffs Department">
      <label for="hcsd">Hughes County Sheriffs Department</label><br>
      
      <input type="checkbox" id="hcfd" name="additionalResponders[]" value="Hughes County Fire Department">
      <label for="hcfd">Hughes County Fire Department</label><br>
      
      <input type="checkbox" id="hcems" name="additionalResponders[]" value="Hughes County EMS">
      <label for="hcems">Hughes County EMS</label><br>
    </div>
  </div>
  
  <!-- ARRIVAL Tab -->
  <div id="arrival" class="tab">
    <h2>ARRIVAL</h2>
    <label for="patientLocation">Patient Found:</label>
    <input type="text" id="patientLocation" placeholder="Upon EMS arrival, the patient was found.... sitting on the porch">
    
    <label for="avpu">AVPU:</label>
    <select id="avpu">
      <option value="alert">Alert</option>
      <option value="responsive to verbal stimulus">Verbal</option>
      <option value="responsive to painful stimulus">Painful</option>
      <option value="unresponsive to any stimulus">Unresponsive</option>
    </select>
    
    <label for="orientation">Orientation:</label>
    <select id="orientation">
      <option value="oriented to self, location, time, and event">x 4</option>
      <option value="x 3">x 3</option>
      <option value="x 2">x 2</option>
      <option value="x 1">x 1</option>
      <option value="x 0">x 0</option>
    </select>
    
    <label for="gcs">GCS:</label>
    <select id="gcs">
      <option value="15">15</option>
      <option value="14">14</option>
      <option value="13">13</option>
      <option value="12">12</option>
      <option value="11">11</option>
      <option value="10">10</option>
      <option value="9">9</option>
      <option value="8">8</option>
      <option value="7">7</option>
      <option value="6">6</option>
      <option value="5">5</option>
      <option value="4">4</option>
      <option value="3">3</option>
    </select>
    
    <label for="gcsQualifier">GCS Qualifier:</label>
    <select id="gcsQualifier">
      <option value="normal for patient">Normal for Patient</option>
      <option value="abnormal for patient">abnormal for patient</option>
    </select>
    
    <label for="airwayAssessment">Airway Assessment:</label>
    <select id="airwayAssessment">
      <option value="patent">Normal</option>
      <option value="partially occluded">Partially Occluded Airway</option>
      <option value="completely occluded, which required immediate intervention">Completely Occluded Airway</option>
      <option value="not open but did not require intervention as the patient was deceased">Deceased, without resuscitation</option>
    </select>
    
    <label for="breathingAssessment">Breathing Assessment:</label>
    <select id="breathingAssessment">
      <option value="spontaneous, non-labored, with regular depth and rate">Normal</option>
      <option value="abnormal but only required minimal intervention">Abnormal, Minimal Intervention</option>
      <option value="critical and was not adequate for perfusion">Abnormal, Not Adequate</option>
      <option value="apneic but did not require intervention as the patient was deceased">Deceased, without resuscitation</option>
    </select>
    
    <label for="circulationAssessment">Circulation Assessment:</label>
    <select id="circulationAssessment">
      <option value="normal with adequate rate and quality">Normal</option>
      <option value="abnormal but only required minimal intervention">Abnormal, Minimal Intervention</option>
      <option value="critical and was not adequate for perfusion">Abnormal, Not Adequate</option>
      <option value="pulseless but did not require intervention as the patient was deceased">Deceased, without resuscitation</option>
    </select>
    
    <label for="primaryAssessment">Primary Assessment:</label>
    <select id="primaryAssessment">
      <option value="no obvious life threats after initial primary assessment">No Obvious Life Threats</option>
      <option value="obvious life threats after initial primary assessment">Obvious Life Threats</option>
    </select>
  </div>
  
  <!-- ASSESSMENT Tab -->
  <div id="assessment" class="tab">
    <h2>ASSESSMENT</h2>
    <label for="assessmentLevel">Assessment:</label>
    <select id="assessmentLevel">
      <option value="BLS assessment">BLS Assessment</option>
      <option value="ALS assessment">ALS Assessment</option>
    </select>
    
    <label for="airwayReassessment">Airway Assessment:</label>
    <select id="airwayReassessment">
      <option value="patent airway">Normal</option>
      <option value="partially occluded airway">Partially Occluded Airway</option>
      <option value="completely occluded airway, which required immediate intervention">Completely Occluded Airway</option>
      <option value="occluded airway which did not require intervention as the patient was deceased">Deceased, without resuscitation</option>
    </select>
    
    <label for="breathingReassessment">Breathing Assessment:</label>
    <select id="breathingReassessment">
      <option value="spontaneous, non-labored, with regular depth and rate">Normal</option>
      <option value="abnormal but only required minimal intervention">Abnormal, Minimal Intervention</option>
      <option value="critical and was not adequate for perfusion">Abnormal, Not Adequate</option>
      <option value="apneic which did not require intervention as the patient was deceased">Deceased, without resuscitation</option>
    </select>
    
    <label for="circulationReassessment">Circulation Assessment:</label>
    <select id="circulationReassessment">
      <option value="normal with adequate rate and quality">Normal</option>
      <option value="abnormal but only required minimal intervention">Abnormal, Minimal Intervention</option>
      <option value="critical and was not adequate for perfusion">Abnormal, Not Adequate</option>
      <option value="pulseless but did not require intervention as the patient was deceased">Deceased, without resuscitation</option>
    </select>
    
    <label for="pastHistory">Past History:</label>
    <input type="text" id="pastHistory" placeholder="past pertinent medical history">    
    
    <label for="chiefComplaint">Chief Complaint:</label>
    <input type="text" id="chiefComplaint" placeholder="what the patient states is the issue">
    
    <label for="primaryImpression">Primary Impression:</label>
    <input type="text" id="primaryImpression" placeholder="differential impression of the provider">
    
    <label for="ecgInterpretation">ECG Interpretation:</label>
    <input type="text" id="ecgInterpretation" placeholder="The paramedic on scene interpretated the ECG as sinus without ectopy">
    
    <label for="vitalSigns">Vital Signs:</label>
    <input type="text" id="vitalSigns" placeholder="... no abnormal vital signs. OR ... a blood pressure of 63/28">
    
    <label for="mobilityAssessment">Mobility Assessment:</label>
    <select id="mobilityAssessment">
      <option value="able to ambulate without assistance">Ambulate Without Assistance</option>
      <option value="able to ambulate with minor assistance">Ambulate with Minor Assistance</option>
      <option value="able to ambulate with maximum assistance">Ambulate with Maximum Assistance</option>
      <option value="unable to ambulate">Unable to Ambulate</option>
    </select>
    
    <label for="assessmentNotes">Additional Assessment Notes:</label>
    <textarea id="assessmentNotes" placeholder="Enter assessment notes"></textarea>
    
    <!-- Reassessment -->
    <h2>REASSESSMENT</h2>
    <label for="reassessTime">Reassessment Time:</label>
    <select id="reassessTime">
      <option value="reassessed every 15 minutes as the patient was stable">15 Minutes</option>
      <option value="reassessed every 5 minutes as the patient was unstable">5 Minutes</option>
    </select>
    
    <label for="reassessmentNotes">Additional Reassessment Notes:</label>
    <textarea id="reassessmentNotes" placeholder="Enter reassessment notes"></textarea>
  </div>
  
  <!-- TREATMENT Tab -->
  <div id="treatment" class="tab">
    <h2>TREATMENT</h2>
       <!-- Airway & Breathing Management -->
    <h3>Airway & Breathing Management</h3>
    <div class="treatment-grid">
      <div class="bubble-option" data-treatment="An OPA was placed to maintain a patent airway.">Oropharyngeal Airway (OPA)</div>
      <div class="bubble-option" data-treatment="An NPA was placed to maintain a patent airway.">Nasopharyngeal Airway (NPA)</div>
      <div class="bubble-option" data-treatment="The patient was suctioned to maintain a patent airway.">Suctioning</div>
      <div class="bubble-option" data-treatment="The patient was ventilated via a bag valve mask to maintain adequate breathing.">Bag-Valve-Mask Ventilation</div>
      <div class="bubble-option" data-treatment="The patient was intubated to secure and maintain the airway.">Endotracheal Intubation</div>
      <div class="bubble-option" data-treatment="The patient was nasotracheally intubated to secure and maintain a patent airway.">Nasotracheal Intubation</div>
      <div class="bubble-option" data-treatment="A king airway was placed to secure and maintain a patent airway.">King Airway</div>
      <div class="bubble-option" data-treatment="A cricothyrotomy was performed to secure and maintain the airway.">Cricothyrotomy</div>
      <div class="bubble-option" data-treatment="The patient was placed on CPAP to maintain adequate breathing.">Continuous Positive Airway Pressure (CPAP)</div>
      <div class="bubble-option" data-treatment="The patient was placed on BiPAP to maintain adequate breathing.">BiPAP</div>
      <div class="bubble-option" data-treatment="The patient was placed on Oxygen to increase the patient's oxygen saturation and improve breathing.">Oxygen Administration</div>
      <div class="bubble-option" data-treatment="The patient was administered a nebulizer treatment to improve breathing.">Nebulizer Treatment</div>
      <div class="bubble-option" data-treatment="The patient was decompressed to improve breathing.">Chest Needle Decompression</div>
    </div>
    
    <!-- Circulation Management -->
    <h3>Circulation Management</h3>
    <div class="treatment-grid">
      <div class="bubble-option" data-treatment="Intravenous (IV) Cannulation" onclick="showIVPopup()">Intravenous (IV) Cannulation</div>
      <div class="bubble-option" data-treatment="Intraosseous (IO) Access" onclick="showIOPopup()">Intraosseous (IO) Access</div>
      <div class="bubble-option" data-treatment="Manual CPR was initiated on the patient due to the patinet being in cardiac arrest.">Manual CPR</div>
      <div class="bubble-option" data-treatment="Mechanical CPR was placed on the patient to ensure adequate rate and depth of CPR.">Mechanical CPR</div>
      <div class="bubble-option" data-treatment="The patient was defibrillated due to a lethal cardiac dysrhythmia.">Defibrillation</div>
      <div class="bubble-option" data-treatment="An automatic electronic defibrillator was used to defibrillate the patient.">AED</div>
      <div class="bubble-option" data-treatment="The patient was cardioverted due to cardiac dysrhythmia.">Cardioversion</div>
      <div class="bubble-option" data-treatment="The patient was transcutaneously paced due to cardiac dysrhythmia.">External Pacing</div>
      <div class="bubble-option" data-treatment="The bleeding was controlled.">Hemorrhage Control</div>
      <div class="bubble-option" data-treatment="The patient's blood glucose was obtained to ensure adequate blood glucose level.">Blood Glucose Monitoring</div>
      <div class="bubble-option" data-treatment="The patient was placed on cardiac monitoring for the duration of patient care.">3-lead ECG Monitoring</div>
      <div class="bubble-option" data-treatment="12-Lead ECG Monitoring" onclick="show12LeadPopup()">12-Lead ECG Monitoring</div>
    </div>
    
    <!-- Medications -->
    <h3>Medications</h3>
    <div class="bubble-option" data-treatment="Medications Administered" onclick="showMedicationPopup()">Medications Administered</div>
    <div id="medicationList" class="medication-list"></div>

    
    <!-- Medication List Display -->
    <div id="medicationList" class="medication-list"></div>
    
    <!-- Trauma Care -->
    <h3>Trauma Care</h3>
    <div class="treatment-grid">
      <div class="bubble-option" data-treatment="The patient's wound was irrigated to lessen the chance of infection.">Wound Irrigation</div>
      <div class="bubble-option" data-treatment="The patient was bandaged due to injury.">Bandaging</div>
      <div class="bubble-option" data-treatment="The patient was splinted due to injury.">Splinting</div>
      <div class="bubble-option" data-treatment="The patient was spinally immobilized due to possible spinal injury.">Spinal Immobilization</div>
      <div class="bubble-option" data-treatment="The patient was cervically immobilized due to possible cervical injury.">Cervical Spinal Immobilization</div>
      <div class="bubble-option" data-treatment="A pelvic binder was placed on the patient due to possible pelvic injury.">Pelvic Binder</div>
      <div class="bubble-option" data-treatment="A traction splint was placed on the patient due to possible mid-line femur fracture.">Traction Splint</div>
      <div class="bubble-option" data-treatment="A bilateral finger thoracostamy was performed due to the patient being in traumatic arrest.">Finger Thoracostamy</div>
    </div>
    
    <!-- Monitoring -->
    <h3>Monitoring</h3>
    <div class="treatment-grid">
      <div class="bubble-option" data-treatment="The patient's vital signs including blood pressure, heart rate, respiratory rate, and SpO2 was monitored during patient care.">Vital Signs Monitoring (BP, HR, RR, SpO2)</div>
      <div class="bubble-option" data-treatment="The patient was placed on cardiac monitoring and cardiac rhythm was monitored during patient care.">Continuous Cardiac Monitoring</div>
      <div class="bubble-option" data-treatment="Capnography was monitored during patient care to ensure adequate ETC02 and respiratory rate.">Capnography</div>
    </div>
    
    <!-- Other Treatments -->
    <h3>Other Treatments</h3>
    <div class="treatment-grid">
      <div class="bubble-option" data-treatment="The patient was positioned as stated in vitals signs tab.">Positioning (Recovery Position, Fowler’s Position)</div>
      <div class="bubble-option" data-treatment="Heat/Cold Therapy">Heat/Cold Therapy</div>
      <div class="bubble-option" data-treatment="The patient was reassured while in care of EMS.">Patient Counseling/Reassurance</div>
      <div class="bubble-option" data-treatment="Other">Other</div>
    </div>
    <div id="otherTextBox" class="text-box" style="display:none;">
      <input type="text" name="otherTreatment" id="otherTreatment" placeholder="Enter additional treatment information...">
    </div>
  </div>
  
  <!-- TRANSPORT/REFUSAL Tab -->
<div id="transport" class="tab">
  <h2>TRANSPORT/REFUSAL</h2>
  
  <label for="transportDecision">Transport Decision:</label>
  <select id="transportDecision">
    <option value="Transport">Transport</option>
    <option value="Refusal">Refusal</option>
  </select>
  
  <!-- Fields for Transport -->
  <div id="transportFields">
    <label for="movementMethod">Movement Method:</label>
    <select id="movementMethod">
      <option value="self ambulation">Self Ambulation</option>
      <option value="assisted ambulation">Assisted Ambulation</option>
      <option value="draw-sheet method">Draw-Sheet Method</option>
      <option value="carried">Carried</option>
      <option value="backboard">Backboard</option>
      <option value="scoop stretcher">Scoop Stretcher</option>
      <option value="mega-mover">Mega Mover</option>
    </select>
    
    <label for="movementReason">Movement Reason:</label>
    <select id="movementReason">
      <option value="without assistance">Self Ambulation</option>
      <option value="due to weakness from current illness">Weakness from Current Illness</option>
      <option value="as to not exacerbate the current illness">Exacerbate from Current Illness</option>
      <option value="due to weakness from current injury">Weakness from Current Injury</option>
      <option value="as to not exacerbate the current Injury">Exacerbate from Current Injury</option>
      <option value="due to weakness from previous stroke">Weakness from Previous Stroke</option>
      <option value="due to weakness from previous amputation">Weakness from Previous Amputation</option>
    </select>
    
    <label for="movementDestination">Movement Destination:</label>
    <select id="movementDestination">
      <option value="EMS stretcher">EMS Stretcher</option>
      <option value="captain's chair">Captain's Chair</option>
      <option value="bench seat">Bench Seat</option>
    </select>
    
    <label for="destinationLocation">Transport Location:</label>
    <input type="text" id="destinationLocation" placeholder="Enter transport location">
    
    <label for="locationReason">Location Reason:</label>
    <select id="locationReason">
      <option value="being the closest appropriate facility">Closest Appropriate Facility</option>
      <option value="being a regional speciality facility">Regional Speciality Facility</option>
      <option value="the physician requesting the facility">Physicians Request</option>
      <option value="the patient requesting the facility against medical advice">Patient Request, Against Medical Advice</option>
      <option value="being diverted from the initial location">Diversion from Initial Location</option>
      <option value="meeting a higher level of care">Rendezvous Area for Transfer of Care to Flight</option>
    </select>
    
    <label for="transportType">Transport Type:</label>
    <select id="transportType">
      <option value="non-emergent without lights and sirens">Non-Emergent</option>
      <option value="emergent with lights and sirens">Emergent</option>
      <option value="non-emergent without lights and sirens which was then upgraded to emergent with lights and sirens">Initial Non-Emergent, Upgraded</option>
      <option value="emergent with lights and sirens which was then downgraded to non-emergent without lights and sirens">Inital Emergent, Downgraded</option>
    </select>
    
    <label for="transportReason">Transport Type Reason:</label>
    <select id="transportReason">
      <option value="patient status">Patient Status</option>
      <option value="protocol">Protocol</option>
      <option value="weather conditions">Weather Conditions</option>
    </select>
    
    <label for="destination">Destination:</label>
    <select id="destination">
      <option value="hospital bed">Hospital Bed</option>
      <option value="bed">Bed</option>
      <option value="chair">Chair</option>
      <option value="wheelchair">Wheel Chair</option>
      <option value="operating table">Operating Table</option>
    </select>
    
    <label for="transferMethod">Transfer Method:</label>
    <select id="transferMethod">
      <option value="self ambulation">Self Ambulation</option>
      <option value="assisted ambulation">Assisted Ambulation</option>
      <option value="draw-sheet method">Draw-Sheet Method</option>
      <option value="carried">Carried</option>
      <option value="backboard">Backboard</option>
      <option value="scoop stretcher">Scoop Stretcher</option>
      <option value="mega-mover">Mega Mover</option>
    </select>
    
    <label for="transferReason">Transfer Reason:</label>
    <select id="transferReason">
      <option value="without assistance">Self Ambulation</option>
      <option value="due to weakness from current illness">Weakness from Current Illness</option>
      <option value="as to not exacerbate the current illness">Exacerbate from Current Illness</option>
      <option value="due to weakness from current injury">Weakness from Current Injury</option>
      <option value="as to not exacerbate the current Injury">Exacerbate from Current Injury</option>
      <option value="due to weakness from previous stroke">Weakness from Previous Stroke</option>
      <option value="due to weakness from previous amputation">Weakness from Previous Amputation</option>
    </select>
    
    <label for="transferCare">Transfer of Care:</label>
    <select id="transferCare">
      <option value="registered nurse">Registered Nurse</option>
      <option value="licensed practical nurse">Licensed Practical Nurse</option>
      <option value="caregiver">Caregiver</option>
      <option value="legal guardian">Legal Guardian</option>
      <option value="power of attorney">Power of Attorney</option>
    </select>
  </div>
  
  <!-- Fields for Refusal -->
  <div id="refusalFields" style="display:none;">
    <label for="refusalParty">Refusal Party:</label>
    <select id="refusalParty">
      <option value="The patient">The Patient</option>
      <option value="The power-of-attorney">The Power-of-Attorney</option>
      <option value="The legal guardian">The Legal Guardian</option>
    </select>
    
    <label for="refusalReason">What is Being Refused:</label>
    <input type="text" id="refusalReason" placeholder="treatment and transport, transport only">
  </div>
</div>

  <!-- GENERATE Tab -->
  <div id="generate" class="tab">
    <h2>GENERATE NARRATIVE</h2>
    <button id="generateBtn">Generate Narrative</button>
    <h3>Narrative Output</h3>
    <div class="narrative-output" id="narrativeOutput"></div>
  </div>
  
  <!-- Medication Popup Modal -->
<div id="medicationPopup" class="popup" style="display:none;">
  <div class="popup-content">
    <span class="close-button" onclick="closeMedicationPopup()">&times;</span>
    <h2>Add Medication</h2>
    <label for="medicationName">Medication Name:</label>
    <input type="text" id="medicationName" placeholder="Enter medication name">
    
    <label for="medicationDose">Dose:</label>
    <input type="text" id="medicationDose" placeholder="Enter dose">
    
    <label for="medicationRoute">Route:</label>
    <input type="text" id="medicationRoute" placeholder="Enter route">
    
    <label for="medicationImprovement">Patient Improvement:</label>
    <select id="medicationImprovement">
      <option value="">Select</option>
      <option value="Improved">Improved</option>
      <option value="No Change">No Change</option>
      <option value="Worsened">Worsened</option>
    </select>
    
    <button class="add-medication" onclick="addMedication()">Add</button>
  </div>
</div>
  
  <!-- Link to external JavaScript file -->
  <script src="hughes_script.js"></script>
</body>
</html>
