// gcems_narrative.js

document.addEventListener("DOMContentLoaded", function() {
    const inputForm = document.getElementById('inputForm');
    const narrativeDiv = document.getElementById('narrative');
    const generateButton = document.getElementById('generateButton');
    const resetButton = document.getElementById('resetButton');
    const copyButton = document.getElementById('copyButton');

    const painCheck = document.getElementById('painCheck');
    const painFields = document.getElementById('painFields');
    const transportToggle = document.getElementById('transportToggle');
    const transportDetails = document.getElementById('transportDetails');
    const refusalToggle = document.getElementById('refusalToggle');
    const refusalDetails = document.getElementById('refusalDetails');
    const refuser = document.getElementById('refuser');
    const refuserSpans = document.querySelectorAll('[id^="refuserSpan"]');

    // Toggle pain section
    painCheck.addEventListener('change', function() {
        painFields.style.display = this.checked ? 'block' : 'none';
    });

    // Toggle transport section
    transportToggle.addEventListener('change', function() {
        transportDetails.style.display = this.checked ? 'block' : 'none';
    });

    // Toggle refusal section
    refusalToggle.addEventListener('change', function() {
        refusalDetails.style.display = this.checked ? 'block' : 'none';
    });

    // Update refusal narrative dynamically based on refuser
    refuser.addEventListener('change', function() {
        refuserSpans.forEach(span => {
            span.textContent = this.value;
        });
    });

    // Generate narrative based on form input
    generateButton.addEventListener('click', function() {
        let narrativeText = '';

        // Dispatch section
        const dispatchDescription = document.getElementById('dispatchDescription').value;
        const responseType = document.getElementById('responseType').value;
        if (dispatchDescription) {
            narrativeText += `<p>Dispatch:<br>The patient was dispatched for ${dispatchDescription}. `;
            narrativeText += `Response was ${responseType}.</p>`;
        }

        // Arrival section
        const patientPosition = document.getElementById('patientPosition').value;
        const locationFound = document.getElementById('locationFound').value;
        const orientationLevel = document.getElementById('orientationLevel').value;
        const gcs = document.getElementById('gcs').value;
        const patientAppearance = document.getElementById('patientAppearance').value;
        if (patientPosition && locationFound && orientationLevel && gcs && patientAppearance) {
            narrativeText += `<p>Arrival:<br>The patient was found ${patientPosition} at ${locationFound}. `;
            narrativeText += `The patient is alert and oriented to ${orientationLevel} with a GCS of ${gcs}. `;
            narrativeText += `The patient appeared to be ${patientAppearance}.</p>`;
        }

        // Chief Complaint
        const chiefComplaint = document.getElementById('chiefComplaint').value;
        if (chiefComplaint) {
            narrativeText += `<p>Chief Complaint:<br>${chiefComplaint}.</p>`;
        }

        // Pain section (if checked)
        if (painCheck.checked) {
            const painOnset = document.getElementById('painOnset').value;
            const provocationWhat = document.getElementById('provocationWhat').value;
            const provocationEffect = document.getElementById('provocationEffect').value;
            const painQuality = document.getElementById('painQuality').value;
            const painRadiation = document.getElementById('painRadiation').value;
            const painSeverity = document.getElementById('painSeverity').value;
            const painDuration = document.getElementById('painDuration').value;
            const painTimeUnit = document.getElementById('painTimeUnit').value;
            if (painOnset && provocationWhat && provocationEffect && painQuality && painRadiation && painSeverity && painDuration && painTimeUnit) {
                narrativeText += `<p>Pain:<br>The patient reported pain as ${painOnset} in onset. `;
                narrativeText += `${provocationWhat} makes the pain ${provocationEffect}. `;
                narrativeText += `Pain is described as ${painQuality} and radiates to the ${painRadiation}. `;
                narrativeText += `Pain severity was reported as ${painSeverity}/10, lasting for ${painDuration} ${painTimeUnit}.</p>`;
            }
        }

        // History
        const pastHistory = document.getElementById('pastHistory').value;
        if (pastHistory) {
            narrativeText += `<p>History:<br>The patient’s past pertinent history includes ${pastHistory}.</p>`;
        }

        // Assessment
        const patientOrientation = document.getElementById('patientOrientation').value;
        const patientAirway = document.getElementById('patientAirway').value;
        const patientBreathing = document.getElementById('patientBreathing').value;
        const patientCirculation = document.getElementById('patientCirculation').value;
        if (patientOrientation && patientAirway && patientBreathing && patientCirculation) {
            narrativeText += `<p>Assessment:<br>The patient is ${patientOrientation}. `;
            narrativeText += `Airway is ${patientAirway}. `;
            narrativeText += `Breathing is ${patientBreathing}. `;
            narrativeText += `Circulation is ${patientCirculation}.</p>`;
        }
        
        // Skin condition
        let skinConditions = [];
        document.querySelectorAll('input[name="skinCondition"]:checked').forEach((el) => {
            skinConditions.push(el.value);
        });
        if (skinConditions.length > 0) {
            narrativeText += `<p>Skin Condition:<br>The patient's skin was noted to be ${skinConditions.join(', ')}.</p>`;
        }

        // Treatment section
        let treatments = [];
        document.querySelectorAll('input[name="treatment"]:checked').forEach((el) => {
            treatments.push(el.labels[0].textContent);
        });
        if (treatments.length > 0) {
            narrativeText += `<p>Treatment:<br>The following treatments were provided: ${treatments.join(', ')}.</p>`;
        }

        // Transport section (if checked)
        if (transportToggle.checked) {
            const transportMethod = document.getElementById('transportMethod').value;
            const transportDestination2 = document.getElementById('transportDestination2').value;
            const transportStatus = document.getElementById('transportStatus').value;
            const patientStatus = document.getElementById('patientStatus').value;
            if (transportMethod && transportDestination2 && transportStatus && patientStatus) {
                narrativeText += `<p>Transport:<br>The patient was transported via ${transportMethod} to ${transportDestination2}. `;
                narrativeText += `Transport status was ${transportStatus}. `;
                narrativeText += `Patient status on arrival at destination was ${patientStatus}.</p>`;
            }
        }

        // Refusal section (if checked)
        if (refusalToggle.checked) {
            const refuserValue = document.getElementById('refuser').value;
            if (refuserValue) {
                narrativeText += `<p>Refusal:<br>The ${refuserValue} refused transport. `;
                narrativeText += `The ${refuserValue} acknowledged the risks and signed the refusal form.</p>`;
            }
        }

        // Medical Necessity
        const medicalNecessityInput = document.getElementById('medicalnecessityInput').value;
        if (medicalNecessityInput) {
            narrativeText += `<p>Medical Necessity:<br>${medicalNecessityInput}.</p>`;
        }

        // Display narrative as HTML content in a div
        narrativeDiv.innerHTML = narrativeText.trim();
    });

    // Reset form and narrative
    resetButton.addEventListener('click', function() {
        inputForm.reset();
        narrativeDiv.innerHTML = '';
        painFields.style.display = 'none';
        transportDetails.style.display = 'none';
        refusalDetails.style.display = 'none';
    });

    // Copy narrative to clipboard
    copyButton.addEventListener('click', function() {
        const tempTextarea = document.createElement('textarea');
        tempTextarea.value = narrativeDiv.innerText;
        document.body.appendChild(tempTextarea);
        tempTextarea.select();
        document.execCommand('copy');
        document.body.removeChild(tempTextarea);
        alert('Narrative copied to clipboard!');
    });
});
