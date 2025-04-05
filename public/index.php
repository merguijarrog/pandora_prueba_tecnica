<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Prueba técnica PandoraFMS</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f7f6;
      margin: 0;
      padding: 0;
      color: #333;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      flex-direction: column;
    }

    h2 {
      color: #3a3a3a;
      font-size: 24px;
      margin-bottom: 10px;
    }

    a {
      display: block;
      background-color: #4CAF50;
      color: white;
      text-decoration: none;
      font-weight: bold;
      margin: 10px 0;
      padding: 15px;
      width: 200px;
      text-align: center;
      border-radius: 5px;
      transition: background-color 0.3s ease;
    }

    a:hover {
      background-color: #45a049;
    }

    section {
      display: none;
      padding: 20px;
      margin: 20px auto;
      max-width: 800px;
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    section.active {
      display: block;
    }

    input[type="text"],
    input[type="tel"],
    input[type="email"],
    select,
    textarea {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 16px;
      box-sizing: border-box;
    }

    button {
      background-color: #4CAF50;
      color: white;
      padding: 12px 25px;
      font-size: 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #45a049;
    }

    #decodedResults {
      margin-top: 20px;
      padding: 10px;
      background-color: #f9f9f9;
      border-radius: 5px;
      border: 1px solid #ddd;
    }

    #error-message,
    #success-message {
      padding: 10px;
      margin-top: 20px;
      border-radius: 5px;
      text-align: center;
      font-weight: bold;
    }

    #error-message {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
      display: none;
    }

    #success-message {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
      display: none;
    }

    #csvInput {
      font-family: monospace;
      font-size: 14px;
      padding: 12px;
      border-radius: 4px;
      border: 1px solid #ccc;
      width: 100%;
      box-sizing: border-box;
    }
  </style>
</head>
<body>

  <a href="#" onclick="showSection('exercise1')">Ejercicio 1</a>
  <a href="#" onclick="showSection('exercise2')">Ejercicio 2</a>

  <!-- Ejercicio 1 -->
  <section id="exercise1">
    <h2>Ejercicio 1 - Decodificar puntuaciones</h2>
    <p>Pega aquí el contenido CSV codificado (nombre, dígitos, puntuación codificada):</p>
    <textarea id="csvInput" rows="10" cols="80" placeholder="user1,abcde,aec..."></textarea><br>
    <button onclick="decodeCSV()">Decodificar</button>
    <div id="decodedResults"></div>
  </section>

  <!-- Ejercicio 2 -->
  <section id="exercise2">
    <h2>Reserva de cita - Clínica de Psicología</h2>
    <form id="appointmentForm">
      <label for="name">Nombre:</label>
      <input type="text" id="name" name="name" required>

      <label for="dni">Número de DNI:</label>
      <input type="text" id="dni" name="dni" required>

      <label for="telephone">Teléfono:</label>
      <input type="tel" id="telephone" name="telephone" required>

      <label for="email">Correo electrónico:</label>
      <input type="email" id="email" name="email" required>

      <label for="type">Tipo de cita:</label>
      <select id="type" name="type" required>
        <option value="1">Primera Consulta</option>
        <option value="2">Revisión</option>
      </select>

      <button type="submit">Reservar cita</button>
    </form>

    <div id="error-message">Por favor, complete todos los campos correctamente</div>
    <div id="success-message"></div>
  </section>

  <script>
    function showSection(id) {
      document.querySelectorAll('section').forEach(sec => sec.classList.remove('active'));
      document.getElementById(id).classList.add('active');
    }

    function decodeCSV() {
      const input = document.getElementById('csvInput').value.trim();
      const lines = input.split('\n');
      let output = "<h3>Resultados:</h3>";

      lines.forEach(line => {
        const parts = line.split(',');
        if (parts.length < 3) {
          output += `<p style="color:red">Línea incorrecta: ${line}</p>`;
          return;
        }
        const [user, digits, encodedScore] = parts;
        const decoded = decodeScore(digits, encodedScore);
        output += `<p><strong>${user}:</strong> ${decoded}</p>`;
      });

      document.getElementById('decodedResults').innerHTML = output;
    }

    function decodeScore(digits, encodedScore) {
      const base = digits.length;
      const digitMap = {};
      for (let i = 0; i < base; i++) {
        digitMap[digits[i]] = i;
      }

      let value = 0;
      for (let i = 0; i < encodedScore.length; i++) {
        const char = encodedScore[i];
        if (digitMap[char] !== undefined) {
          value = value * base + digitMap[char];
        }
      }
      return value;
    }

    //check form
    function validateForm() {
      const name = document.getElementById('name').value;
      const dni = document.getElementById('dni').value;
      const telephone = document.getElementById('telephone').value;
      const email = document.getElementById('email').value;
      const type = document.getElementById('type').value;

      const nameRegex = /^[a-zA-Z\s]+$/;
      const dniRegex = /^\d{8}[A-Za-z]$/;
      const telephoneRegex = /^[0-9]{9}$/;
      const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

      if (!nameRegex.test(name)) return alert('El nombre solo puede contener letras y espacios.') && false;
      if (!dniRegex.test(dni)) return alert('DNI inválido. El formato correcto es: 12345678A') && false;
      if (!telephoneRegex.test(telephone)) return alert('Número de teléfono inválido. Debe ser de 9 dígitos.') && false;
      if (!emailRegex.test(email)) return alert('Dirección de correo electrónico inválida.') && false;
      if (!type) return alert('Por favor, seleccione un tipo de cita.') && false;

      return true;
    }

    //check if dni exist in db
    document.getElementById('dni').addEventListener('blur', function () {
      const dniValue = this.value;
      if (!dniValue.match(/^\d{8}[A-Za-z]$/)) return;

      fetch(`../src/controllers/save_form.php?dni=${dniValue}`)
        .then(response => response.json())
        .then(data => {
          const typeSelect = document.getElementById('type');
          typeSelect.innerHTML = data.exists
            ? `<option value="1">Primera Consulta</option><option value="2">Revisión</option>`
            : `<option value="1">Primera Consulta</option>`;
        })
        .catch(error => console.error('Error al verificar DNI:', error));
    });

    document.getElementById('appointmentForm').addEventListener('submit', function (e) {
      e.preventDefault();

      const submitButton = document.querySelector('button[type="submit"]');
      submitButton.disabled = true;

      if (!validateForm()) {
        submitButton.disabled = false;
        document.getElementById('error-message').style.display = 'block';
        document.getElementById('success-message').style.display = 'none';
        return;
      }

      const formData = new FormData(this);

      fetch('../src/controllers/save_form.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          submitButton.disabled = false;

          if (data.status === 'success') {
            document.getElementById('success-message').innerHTML = data.message + " - " + data.appointment_datetime;
            document.getElementById('success-message').style.display = 'block';
            document.getElementById('error-message').style.display = 'none';
            document.getElementById('appointmentForm').reset();  //clean form
          } else {
            document.getElementById('error-message').innerHTML = data.message;
            document.getElementById('error-message').style.display = 'block';
            document.getElementById('success-message').style.display = 'none';
          }
        })
        .catch(error => {
          submitButton.disabled = false;
          console.error('Error al procesar el formulario:', error);
          document.getElementById('error-message').innerHTML = 'Hubo un error al procesar la solicitud. Intente más tarde.';
          document.getElementById('error-message').style.display = 'block';
        });
    });
  </script>

</body>
</html>