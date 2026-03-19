<?php
session_start();

// Ensure the user is logged in; a license number is not mandatory
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $narrative = '';

    // Dispatch Section
    if (!empty($_POST['dispatchDescription']) && !empty($_POST['responseType'])) {
        $narrative .= "DISPATCH:\n";
        $narrative .= "The patient was dispatched for " . htmlspecialchars($_POST['dispatchDescription']) . ". ";
        $narrative .= "Response was " . htmlspecialchars($_POST['responseType']) . ".\n\n";
    }

    // Arrival Section
    if (!empty($_POST['patientPosition']) && !empty($_POST['locationFound']) && !empty($_POST['orientationLevel']) && !empty($_POST['gcs']) && !empty($_POST['patientAppearance'])) {
        $narrative .= "ARRIVAL:\n";
        $narrative .= "The patient was found " . htmlspecialchars($_POST['patientPosition']) . " at " . htmlspecialchars($_POST['locationFound']) . ". ";
        $narrative .= "The patient is alert and oriented to " . htmlspecialchars($_POST['orientationLevel']) . " with a GCS of " . htmlspecialchars($_POST['gcs']) . ". ";
        $narrative .= "The patient appeared to be " . htmlspecialchars($_POST['patientAppearance']) . ".\n\n";
    }

    // Chief Complaint Section
    if (!empty($_POST['chiefComplaint'])) {
        $narrative .= "CHIEF COMPLAINT:\n";
        $narrative .= htmlspecialchars($_POST['chiefComplaint']) . ".\n\n";
    }

    // Pain Section
    if (!empty($_POST['painOnset']) || !empty($_POST['provocationWhat']) || !empty($_POST['painQuality'])) {
        $narrative .= "PAIN:\n";
        $narrative .= "Pain onset was " . htmlspecialchars($_POST['painOnset']) . ". ";
        $narrative .= htmlspecialchars($_POST['provocationWhat']) . " provokes the pain, making it " . htmlspecialchars($_POST['provocationEffect']) . ". ";
        $narrative .= "The pain is described as " . htmlspecialchars($_POST['painQuality']) . ".\n\n";
    }

    // History Section
    if (!empty($_POST['pastHistory'])) {
        $narrative .= "HISTORY:\n";
        $narrative .= htmlspecialchars($_POST['pastHistory']) . ".\n\n";
    }

    // Assessment Section
    if (!empty($_POST['patientOrientation']) || !empty($_POST['airwayStatus']) || !empty($_POST['breathingStatus']) || !empty($_POST['circulationStatus'])) {
        $narrative .= "INITIAL ASSESSMENT:\n";
        $narrative .= "The patient was " . htmlspecialchars($_POST['patientOrientation']) . ". ";
        $narrative .= "Airway was " . htmlspecialchars($_POST['airwayStatus']) . ". ";
        $narrative .= "Breathing was " . htmlspecialchars($_POST['breathingStatus']) . ". ";
        $narrative .= "Circulation was " . htmlspecialchars($_POST['circulationStatus']) . ".\n\n";
    }

    // Reassessment Section
    if (!empty($_POST['patientStatus']) || !empty($_POST['reassessOrientation']) || !empty($_POST['reassessAirway']) || !empty($_POST['reassessBreathing']) || !empty($_POST['reassessCirculation'])) {
        $narrative .= "REASSESSMENT:\n";
        $narrative .= "The patient's status was " . htmlspecialchars($_POST['patientStatus']) . ". ";
        $narrative .= "Orientation was " . htmlspecialchars($_POST['reassessOrientation']) . ". ";
        $narrative .= "Airway was " . htmlspecialchars($_POST['reassessAirway']) . ". ";
        $narrative .= "Breathing was " . htmlspecialchars($_POST['reassessBreathing']) . ". ";
        $narrative .= "Circulation was " . htmlspecialchars($_POST['reassessCirculation']) . ".\n\n";
    }

    // Treatment Section
    if (!empty($_POST['treatmentDetails'])) {
        $narrative .= "TREATMENT:\n";
        $narrative .= htmlspecialchars($_POST['treatmentDetails']) . ".\n\n";
    }

    // Transport Section
    if (!empty($_POST['transportDestination']) || !empty($_POST['movementMethod']) || !empty($_POST['movementDestination']) || !empty($_POST['transportStatus'])) {
        $narrative .= "TRANSPORT:\n";
        $narrative .= "The patient was transported to " . htmlspecialchars($_POST['transportDestination']) . " using " . htmlspecialchars($_POST['movementMethod']) . " to " . htmlspecialchars($_POST['movementDestination']) . ". ";
        $narrative .= "Transport was " . htmlspecialchars($_POST['transportStatus']) . " with " . htmlspecialchars($_POST['transportLights']) . ".\n\n";
    }

    // Refusal Section
    if (!empty($_POST['refuser'])) {
        $narrative .= "REFUSAL:\n";
        $narrative .= "The " . htmlspecialchars($_POST['refuser']) . " refused care.\n\n";
    }

    // Medical Necessity Section
    if (!empty($_POST['medicalNecessity'])) {
        $narrative .= "MEDICAL NECESSITY:\n";
        $narrative .= htmlspecialchars($_POST['medicalNecessity']) . ".\n\n";
    }

    // Add username and license number at the bottom
    if (isset($_SESSION['username'])) {
        $username = htmlspecialchars($_SESSION['username']);
        $narrative .= "\n" . $username;
    } else {
        $narrative .= "\n" . "Username not set.";
    }

    if (isset($_SESSION['license_number'])) {
        $licenseNumber = htmlspecialchars($_SESSION['license_number']);
        if (!empty($licenseNumber)) {
            $narrative .= " | License #: " . $licenseNumber;
        }
    }

    // Output the generated narrative
    echo nl2br($narrative);
} else {
    echo "Invalid request method.";
}
?>
