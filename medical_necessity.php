<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Necessity Generator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 30px;
            max-width: 800px;
            margin: auto;
        }
        h1 {
            text-align: center;
            color: #003366;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 20px;
        }
        select, textarea, input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        button {
            margin-top: 20px;
            padding: 12px 20px;
            background-color: #003366;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #001f4d;
        }
        #outputBox {
            margin-top: 30px;
        }
        #output {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            white-space: pre-wrap;
        }
        .intervention-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        .intervention-grid label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            text-align: center;
            box-shadow: 1px 1px 3px rgba(0,0,0,0.1);
            transition: background-color 0.3s, color 0.3s;
        }
        .intervention-grid input[type="checkbox"] {
            display: none;
        }
        .intervention-grid input[type="checkbox"]:checked + span {
            background-color: #003366;
            color: #fff;
            border-color: #003366;
        }
    </style>
</head>
<body>
    <h1>Medical Necessity Generator</h1>
    <label for="primaryImpression">Primary Impression:</label>
    <input type="text" id="primaryImpressionSearch" onkeyup="filterPrimaryImpressions()" placeholder="Search Primary Impressions...">
    <select id="primaryImpression" size="10"></select>

    <label for="interventions">Interventions Performed:</label>
    <div id="interventionGroup" class="intervention-grid"></div>

    <label for="contraindicatedReason">Why Transport by Other Means Was Contraindicated:</label>
    <select id="contraindicatedReason">
        <option value="">-- Select Contraindication --</option>
        <option value="risk of rapid deterioration without EMS support">Risk of rapid deterioration without EMS support</option>
        <option value="need for continuous medical supervision">Need for continuous medical supervision</option>
        <option value="inability to ambulate or tolerate upright position">Inability to ambulate or tolerate upright position</option>
        <option value="requirement for advanced care and monitoring">Requirement for advanced care and monitoring</option>
        <option value="patient behavior posed safety concern">Patient behavior posed safety concern</option>
        <option value="absence of capable caregiver at scene">Absence of capable caregiver at scene</option>
        <option value="requirement for specialized transport equipment">Requirement for specialized transport equipment</option>
        <option value="risk of airway compromise en route">Risk of airway compromise en route</option>
        <option value="need for continuous medication administration">Need for continuous medication administration</option>
        <option value="need for advanced respiratory support">Need for advanced respiratory support</option>
        <option value="need for cardiac monitoring en route">Need for cardiac monitoring en route</option>
        <option value="hemodynamic instability">Hemodynamic instability</option>
        <option value="altered level of consciousness requiring monitoring">Altered level of consciousness requiring monitoring</option>
        <option value="risk of exacerbation of symptoms during transport">Risk of exacerbation of symptoms during transport</option>
        <option value="combative or unpredictable patient requiring restraint">Combative or unpredictable patient requiring restraint</option>
        <option value="need for continuous assessment and documentation">Need for continuous assessment and documentation</option>
        <option value="potential for spinal injury requiring immobilization">Potential for spinal injury requiring immobilization</option>
        <option value="significant pain limiting mobility and function">Significant pain limiting mobility and function</option>
        <option value="orthopedic instability requiring stabilization devices">Orthopedic instability requiring stabilization devices</option>
        <option value="bleeding requiring EMS hemorrhage control">Bleeding requiring EMS hemorrhage control</option>
        <option value="increased risk of worsening injury during non-EMS transport">Increased risk of worsening injury during non-EMS transport</option>
    </select>

    <button onclick="generateStatement()">Generate Medical Necessity Statement</button>
    <div id="outputBox">
        <h2>Generated Statement</h2>
        <div id="output"></div>
    </div>

    <script>
        const impressions = ["Abdominal Pain", "Acute Coronary Syndrome", "Acute pain", "Alcohol dependence with withdrawal", "Alcohol intoxication", "Allergic Reaction", "Altered Mental Status", "Amputation", "Anaphylactic Shock", "Anaphylaxis", "Angina", "Anxiety", "Aphagia", "Aphasia", "Ascites", "Asthma", "Back Pain", "Behavioral episode", "Psychiatric Episode", "Bell's Palsy", "Birth injuries to the newborn", "Brain injury", "Burn", "Cancer", "Carbon Monoxide poisoning", "Cardiac Arrest", "Cardiac arrhythmia", "Cardiac tamponade", "Cardiogenic shock", "Cellulitis", "Chest pain on breathing", "COPD", "Coma", "Concussion", "Congestive Heart Failure", "Constipation", "Contact with venomous animal", "Contact with venomous plant", "Convulsions", "Cough", "COVID-19", "DVT", "Dehydration", "Deliberate self harm", "Dementia", "Diabetic hyperglycemia", "Diabetic hypoglycemia", "Diarrhea", "Displacement of urinary catheter", "Dizziness", "Drowning", "Ectopic pregnancy", "Edema", "Electrocution", "Encounter for full term uncomplicated delivery", "ESRD", "Epidural hemorrhage", "Epistaxis", "Esophageal obstruction", "Extremity pain", "Eye injury", "Eye pain", "Failure to thrive", "Fatigue", "Febrile seizures", "Fever", "Foreign body in the body", "Frostbite", "GERD", "GI hemorrhage", "Headache", "Heat exhaustion", "Heat stroke", "Hematemesis", "Hematoma", "Hemiparesis", "Hemiplegia", "Hemorrhage", "Hemorrhagic shock", "Hemothorax", "Homicidal ideation", "Hyperglycia (nondiabetic)", "Hypertension", "Hypertensive crisis", "Hyperventilation", "Hypoglycemia (nondiabetic)", "Hypotension", "Hypothermia", "Hypovolemia", "Hypovolemia/Shock", "Hypoxemia", "Hypoxia", "Influenza", "Traumatic injury", "Intestinal obstruction", "Intracranial hemorrhage", "Kidney stones", "Labor and delivery with complications", "Labor and delivery without complications", "Laceration (major)", "Laceration, abrasion, or hematoma (minor surface trauma)", "Laryngitis/Croup", "Liver failure", "Lower back pain", "Malaise", "Medical device failure", "Meningitis", "Metabolic shock", "Migraine", "Multiple injuries", "Nausea", "Near syncope", "Neurogenic shock", "NSTEMI", "Obstetric trauma", "Obvious death", "Orthostatic hypotension", "Overdose", "Pain (non-traumatic)", "Pain, acute due to trauma", "Pain, chronic", "Palpitations", "Paralysis", "Paraplegia", "Pitting edema", "Pneumonia", "Pneumothorax", "Poisoning/Drug ingestion", "Pre-eclampsia", "Pregnancy complications", "Pregnancy with contractions", "Preterm labor", "Pulmonary edema, acute", "Pulmonary embolism", "Rash", "Renal failure", "Respiratory arrest", "Respiratory failure", "RSV", "Seizures with status epilepticus", "Seizures without status epilepticus", "Sepsis/Septicemia", "Septic shock", "SARS", "Shortness of breath", "Sickle cell crisis", "Smoke inhalation", "Spontaneous abortion (miscarriage)", "STEMI", "Stroke", "Subarachnoid hemorrhage", "Subdural hemorrhage", "Substance abuse", "Suffocation or asphyxia", "Suicidal ideation", "Suicide attempt", "Syncope/fainting", "Toothache", "Tracheostomy problem", "TIA", "Traumatic circulatory arrest", "Unconscious", "Urinary system disorder", "Urinary tract infection", "Vomiting"];

        const interventions = ["Oxygen administration", "IV access", "IO access", "Cardiac monitoring", "4-lead ECG", "12-lead ECG", "Vital signs monitoring including heart rate, respiratory rate, SpO2, and blood pressure", "CPR performed", "Defibrillation", "Advanced Life Support Medications", "Basic Life Support Medications", "Nebulizer treatment", "Airway adjunct (OPA/NPA)", "Endotracheal intubation", "Surgical Cricothyrotomy", "Supraglottic Airway", "CPAP", "BiPAP", "Capnography monitoring", "Blood glucose check", "Spinal immobilization", "Cervical collar application", "Pelvic binder applied", "Traction splint", "Bandaging", "Hemorrhage control", "Burn care", "External pacing", "Cardioversion", "Advanced airway placement", "Chest decompression", "Transport with monitoring"];

        const primaryImpressionSelect = document.getElementById('primaryImpression');
        impressions.forEach(impression => {
            const option = document.createElement('option');
            option.value = impression;
            option.textContent = impression;
            primaryImpressionSelect.appendChild(option);
        });

        const interventionGrid = document.getElementById('interventionGroup');
        interventions.forEach(intervention => {
            const label = document.createElement('label');
            const checkbox = document.createElement('input');
            const span = document.createElement('span');
            checkbox.type = 'checkbox';
            checkbox.name = 'intervention';
            checkbox.value = intervention;
            span.textContent = intervention;
            label.appendChild(checkbox);
            label.appendChild(span);
            interventionGrid.appendChild(label);
        });

        function filterPrimaryImpressions() {
            const input = document.getElementById("primaryImpressionSearch").value.toLowerCase();
            const options = primaryImpressionSelect.getElementsByTagName("option");
            for (let i = 0; i < options.length; i++) {
                const txtValue = options[i].textContent || options[i].innerText;
                options[i].style.display = txtValue.toLowerCase().includes(input) ? "" : "none";
            }
        }

        function formatInterventions(interventions) {
            if (interventions.length === 1) return interventions[0];
            if (interventions.length === 2) return interventions.join(" and ");
            return interventions.slice(0, -1).join(", ") + ", and " + interventions.slice(-1);
        }

        function generateStatement() {
            const impression = document.getElementById('primaryImpression').value;
            const contraindication = document.getElementById('contraindicatedReason').value;
            const interventionCheckboxes = document.querySelectorAll('#interventionGroup input[type=checkbox]:checked');
            const selectedInterventions = Array.from(interventionCheckboxes).map(cb => cb.value);

            if (!impression || !contraindication || selectedInterventions.length === 0) {
                document.getElementById('output').textContent = "Please complete all fields to generate a statement.";
                return;
            }

            const formattedInterventions = formatInterventions(selectedInterventions);

            const statement = `The patient required ambulance transport due to a medical necessity which consisted of ${impression} and required interventions of the following: ${formattedInterventions}. Transport by other means was contraindicated due to ${contraindication}.`;

            document.getElementById('output').textContent = statement;
        }
    </script>
</body>
</html>
