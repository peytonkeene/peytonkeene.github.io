function togglePainFields() {
    var painSection = document.getElementById('painFields');
    if (document.getElementById('painCheck').checked) {
        painSection.style.display = 'block';
    } else {
        painSection.style.display = 'none';
    }
}

function generateNarrative() {
    // Existing narrative generation code

    // Pain section
    var painNarrative = '';
    if (document.getElementById('painCheck').checked) {
        const painOnset = document.getElementById('painOnset').value;
        const provocationWhat = document.getElementById('provocationWhat').value;
        const provocationEffect = document.getElementById('provocationEffect').value;
        const painQuality = document.getElementById('painQuality').value;
        const painRadiation = document.getElementById('painRadiation').value;
        const painSeverity = document.getElementById('painSeverity').value;
        const painDuration = document.getElementById('painDuration').value;
        const painTimeUnit = document.getElementById('painTimeUnit').value;

        painNarrative = `Pain Assessment: Onset is ${painOnset}. Provocation: ${provocationWhat} makes the pain ${provocationEffect}. Quality: ${painQuality}. Radiation: ${painRadiation}. Severity is ${painSeverity}. Time: ${painDuration} ${painTimeUnit}.`;
    }

    // Combine narratives
    const fullNarrative = `${dispatchNarrative} ${assessmentNarrative} ${chiefComplaintNarrative} ${painNarrative}`;

    document.getElementById('narrativeOutput').textContent = fullNarrative;
}
