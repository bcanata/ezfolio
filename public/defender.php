<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$recordCreated = false;
$recordId = null;

$servername = "localhost";
$username = "thedewil_feelida";
$password = "Feelida123..";
$dbname = "thedewil_feelida";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check total number of entries (rooms booked)
$totalRoomsQuery = "SELECT COUNT(*) AS total_rooms FROM registrations";
$totalRoomsResult = $conn->query($totalRoomsQuery);
$totalRooms = $totalRoomsResult->fetch_assoc()['total_rooms'];

// Check total number of participants
$totalParticipantsQuery = "SELECT SUM(attendance) AS total_participants FROM registrations";
$totalParticipantsResult = $conn->query($totalParticipantsQuery);
$totalParticipants = $totalParticipantsResult->fetch_assoc()['total_participants'];

$disableTwoParticipantsOption = false;

if ($totalParticipants == 21) {
    // Disable "İKİ KİŞİ" option if there are 21 participants
    $disableTwoParticipantsOption = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $totalRooms < 14 && $totalParticipants < 22) {

    // Sanitize input data
    $date = $conn->real_escape_string($_POST['date'] ?? '');
    $attendance = $conn->real_escape_string($_POST['attendance'] ?? '');
    $firstName = $conn->real_escape_string($_POST['first_name'] ?? '');
    $lastName = $conn->real_escape_string($_POST['last_name'] ?? '');
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $kvkk = isset($_POST['kvkk']) ? 1 : 0;
    $travelAgreement = isset($_POST['travel']) ? 1 : 0;
    $submissionTime = date("Y-m-d H:i:s");

    // SQL statement to insert data
    $stmt = $conn->prepare("INSERT INTO registrations (date, attendance, first_name, last_name, phone, email, kvkk, travel_agreement, submission_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssiss", $date, $attendance, $firstName, $lastName, $phone, $email, $kvkk, $travelAgreement, $submissionTime);

    if ($stmt->execute()) {
        $recordCreated = true;
        $recordId = $conn->insert_id; // Fetch the ID of the newly created record
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Defender Experience Kaz Dağları</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://db.onlinewebfonts.com/c/11d164da9b46e6f7955e396757e5ca70?family=AvenirNextCyr-Regular" rel="stylesheet">
    <style>
        @font-face {
            font-family: "AvenirNextCyr-Regular";
            src: url("https://db.onlinewebfonts.com/t/11d164da9b46e6f7955e396757e5ca70.eot");
            src: url("https://db.onlinewebfonts.com/t/11d164da9b46e6f7955e396757e5ca70.eot?#iefix") format("embedded-opentype"), 
                url("https://db.onlinewebfonts.com/t/11d164da9b46e6f7955e396757e5ca70.woff2") format("woff2"), 
                url("https://db.onlinewebfonts.com/t/11d164da9b46e6f7955e396757e5ca70.woff") format("woff"), 
                url("https://db.onlinewebfonts.com/t/11d164da9b46e6f7955e396757e5ca70.ttf") format("truetype"), 
                url("https://db.onlinewebfonts.com/t/11d164da9b46e6f7955e396757e5ca70.svg#AvenirNextCyr-Regular") format("svg");
        }
        body {
            font-family: "AvenirNextCyr-Regular";
        }
    </style>
</head>
<body class="antialiased text-gray-900 leading-normal tracking-wider bg-cover" style="background-image:url('background.png');">
    <div class="snap-start w-full flex flex-col bg-white py-8">
        <h6 class="text-lg font-semibold mt-8 ml-8">DEFENDER EXPERIENCE KAZ DAĞLARI</h6>
        <div class="flex flex-col items-center justify-center min-h-screen bg-white">

            <?php if ($recordCreated): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                    <p class="font-bold">Başarılı!</p>
                    <p>Kaydınız başarı ile tamamlandı. Kayıt numaranız: <?= $recordId ?></p>
                </div>
            <?php elseif ($totalRooms >= 14 || $totalParticipants >= 22): ?>
                <div class="bg-black-100 border-l-4 border-black-500 text-black-700 p-4" role="alert">
                    <p>Kayıtlarımız tamamlanmıştır, ilginiz için teşekkür ederiz</p>
                </div>
            <?php else: ?>
            <form class="w-full max-w-2xl px-6 py-10 bg-white" action="https://bugracanata.com.tr/defender.php" method="POST">
                <h2 class="mt-4 text-xl font-light">ÖDEME EKRANI</h2>
                <!-- Date selection section -->
                <div class="mt-8">
                    <label class="block mb-2 text-sm font-semibold text-gray-700 mb-10">LÜTFEN BİR TARİH SEÇİNİZ.</label>
                    <div class="flex justify-between mb-6">
                        <input type="radio" id="date1" name="date" value="9 - 11 MAYIS 2024" class="hidden" />
                        <label for="date1" class="w-1/2 mr-2 px-4 py-3 text-sm font-semibold text-gray-700 bg-gray-200 focus:outline-none cursor-pointer">9 - 11 MAYIS 2024</label>
                        
                        <input type="radio" id="date2" name="date" value="12 - 14 MAYIS 2024" class="hidden" />
                        <label for="date2" class="w-1/2 ml-2 px-4 py-3 text-sm font-semibold text-gray-700 bg-gray-200 focus:outline-none cursor-pointer">12 - 14 MAYIS 2024</label>
                    </div>
                </div>
    
                <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
    
                <!-- Participant selection section -->
                <div class="mt-8">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">KATILIMCI SAYISI</label>
                    <div class="mb-10">
                        <label class="inline-flex items-center">
                            <input type="radio" class="form-radio w-8 h-8" name="attendance" value="1">
                            <span class="ml-2">TEK KİŞİ</span>
                        </label>
                        <label class="inline-flex items-center ml-6">
                            <input type="radio" class="form-radio w-8 h-8" name="attendance" value="2">
                            <span class="ml-2">İKİ KİŞİ</span>
                        </label>
                    </div>
                </div>

                <!-- Total price section -->
                <div class="mt-8 flex flex-col">
                    <label class="text-sm font-semibold text-gray-700">TOPLAM FİYAT</label>
                    <div class="mt-2 w-96 p-4 bg-gray-200">
                        <span id="totalPrice" class="text-gray-700 text-center block">XXX.XXX TL</span>
                    </div>
                </div>
    
                <!-- Personal details section -->
                <div class="mt-8">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">KİŞİSEL BİLGİLER</label>
                    <div class="grid grid-cols-2 gap-4">
                    <input id="first_name" type="text" name="first_name" placeholder="Adınız" required class="mb-4 w-full px-4 py-3 bg-gray-200 border-0">
                    <input id="last_name" type="text" name="last_name" placeholder="Soyadınız" required class="mb-4 w-full px-4 py-3 bg-gray-200 border-0">
                    <input id="phone" type="text" name="phone" placeholder="Telefon Numaranız" required class="mb-4 w-full px-4 py-3 bg-gray-200 border-0">
                    <input id="email" type="email" name="email" placeholder="E-Posta Adresiniz" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" class="mb-4 w-full px-4 py-3 bg-gray-200 border-0">
                    </div>
                </div>

                <div class="mt-8">
                    <!-- Checkbox for ETK ve KVKK -->
                    <label class="flex items-center mb-4">
                        <input id="kvkk" name="kvkk" type="checkbox" class="form-checkbox w-8 h-8" value="1" required>
                        <span class="ml-2 text-sm">ETK ve KVKK okudum ve kabul ediyorum.</span>
                    </label>
                                    
                    <!-- Checkbox for Seyahat sözleşmesi -->
                    <label class="flex items-center">
                        <input id="travel" name="travel" type="checkbox" class="form-checkbox w-8 h-8" value="1" required>
                        <span class="ml-2 text-sm">Seyahat sözleşmesini okudum ve kabul ediyorum.</span>
                    </label>
                </div>
                

                <!-- Submit button -->
                <div class="mt-8">
                    <button type="submit" class="w-full px-4 py-3 bg-gray-500 text-white font-semibold">KAYIT OL</button>
                </div>
            </form>
            <?php endif; ?>                                      
        </div>
    </div>
    

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateRadios = document.querySelectorAll('input[type="radio"][name="date"]');
            const attendanceRadios = document.querySelectorAll('input[type="radio"][name="attendance"]');
            
            // Initial highlighting based on the checked state
            highlightDateSelection();
        
            // Setup for date selection highlighting and price update
            dateRadios.forEach(radio => {
                radio.addEventListener('change', () => {
                    highlightDateSelection();
                    updatePrice();
                });
            });
        
            // Update price based on selection of attendance
            attendanceRadios.forEach(radio => radio.addEventListener('change', updatePrice));
            updatePrice(); // Initial call to set default price
        });
        
        function highlightDateSelection() {
            document.querySelectorAll('label[for^="date"]').forEach(label => {
                // Determine if the associated radio button is checked
                const associatedRadio = document.querySelector('#' + label.getAttribute('for'));
                if (associatedRadio && associatedRadio.checked) {
                    label.classList.add('text-white', 'bg-black'); // Highlight selected
                    label.classList.remove('text-gray-700', 'bg-gray-200'); // Reset others
                } else {
                    label.classList.remove('text-white', 'bg-black');
                    label.classList.add('text-gray-700', 'bg-gray-200'); // Reset others
                }
            });
        }
        
        function updatePrice() {
            const selectedRadio = document.querySelector('input[type="radio"][name="attendance"]:checked');
            let priceText = "XXX.XXX TL"; // Default text
        
            if (selectedRadio) {
                const pricePerPerson = 90000; // Fixed price per person
                let totalPrice = selectedRadio.value === "1" ? pricePerPerson : pricePerPerson * 2;
                priceText = `${totalPrice.toLocaleString('tr-TR')} TL`;
            }
        
            document.getElementById('totalPrice').textContent = priceText;
        }
        </script>
              
</body>

</html>
