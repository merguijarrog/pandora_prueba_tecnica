<?php
require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo json_encode(['status' => 'error', 'message' => 'Error de conexión a la base de datos']);
  exit();
}

//check dni
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['dni'])) {
  $dni = htmlspecialchars($_GET['dni']);
  $stmt = $pdo->prepare("SELECT id FROM patient WHERE dni = ?");
  $stmt->execute([$dni]);
  $patient = $stmt->fetch();

  if ($patient) {
    echo json_encode(['exists' => true]);
  } else {
    echo json_encode(['exists' => false]);
  }
  exit();
}

//reservation process
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  //check params
  if (!isset($_POST['name'], $_POST['dni'], $_POST['telephone'], $_POST['email'], $_POST['type'])) {
    echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios']);
    exit();
  }

  //get data
  $name = htmlspecialchars($_POST['name']);
  $dni = htmlspecialchars($_POST['dni']);
  $telephone = htmlspecialchars($_POST['telephone']);
  $email = htmlspecialchars($_POST['email']);
  $type = (int)htmlspecialchars($_POST['type']);

  //start transaction to ensure atomic operations
  try {
    $pdo->beginTransaction();

    //check if exist in db
    $stmt = $pdo->prepare("SELECT id FROM patient WHERE dni = ?");
    $stmt->execute([$dni]);
    $patient = $stmt->fetch();

    if (!$patient) {
      //if patient does not exist
      $stmt = $pdo->prepare("INSERT INTO patient (name, dni, telephone, email) VALUES (?, ?, ?, ?)");
      $stmt->execute([$name, $dni, $telephone, $email]);
      $patient_id = $pdo->lastInsertId();
      $type = 1;
    } else {
      //if patient exists
      $patient_id = $patient['id'];
      $type = 2; 
    }

    //define time range
    $start_time = strtotime('10:00 AM');
    $end_time = strtotime('10:00 PM');
    $interval = 3600; // 1 hour intervals

    //get the current day
    $today = strtotime('today');

    //search for existing booked appointments
    $stmt = $pdo->prepare("SELECT appointment_date FROM appointment WHERE appointment_date LIKE ?");
    $stmt->execute([date('Y-m-d', $today) . '%']);
    $booked_slots = $stmt->fetchAll(PDO::FETCH_COLUMN);

    //search first available slot
    function find_first_available_slot($pdo, $date, $start_time, $end_time, $interval, $booked_slots) {
      for ($time = $start_time; $time < $end_time; $time += $interval) {
        $full_appointment_datetime = date('Y-m-d', $date) . ' ' . date('H:i:s', $time);

        if (!in_array($full_appointment_datetime, $booked_slots)) {
          return $full_appointment_datetime;
        }
      }
      return false;
    }

    //check available slot
    $available_slot = find_first_available_slot($pdo, $today, $start_time, $end_time, $interval, $booked_slots);

    //if no available slot today, search the next day
    if (!$available_slot) {
      $next_day = strtotime('next day', $today); // get next day
      $stmt->execute([date('Y-m-d', $next_day) . '%']);
      $booked_slots = $stmt->fetchAll(PDO::FETCH_COLUMN);
      $available_slot = find_first_available_slot($pdo, $next_day, $start_time, $end_time, $interval, $booked_slots);
    }

    //check if an available slot was found
    if ($available_slot) {
      //insert new appointment into the database
      $stmt = $pdo->prepare("INSERT INTO appointment (patient_id, appointment_date, appointment_type_id) VALUES (?, ?, ?)");
      $stmt->execute([$patient_id, $available_slot, $type]);

      //commit the transaction
      $pdo->commit();

      echo json_encode(['status' => 'success', 'message' => 'Cita reservada con éxito', 'appointment_datetime' => $available_slot]);
    } else {
      //no available slots for the next days
      $pdo->rollBack();
      echo json_encode(['status' => 'error', 'message' => 'No hay horas disponibles para los próximos días']);
    }
  } catch (Exception $e) {
    //rollback transaction if any error occurs
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Hubo un problema al reservar la cita. Inténtalo de nuevo']);
  }
}
?>
