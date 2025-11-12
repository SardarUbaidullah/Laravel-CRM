<!DOCTYPE html>
<html>
<head>
    <title>🚨 ULTIMATE WEBSITE DESTRUCTION 🚨</title>
    <style>
        body {
            background: #000;
            color: #ff0000;
            font-family: monospace;
            text-align: center;
            padding: 50px;
        }
        .warning {
            border: 5px solid #ff0000;
            padding: 30px;
            margin: 20px;
            background: #330000;
        }
        .nuclear-button {
            background: linear-gradient(45deg, #ff0000, #ff6b6b);
            color: white;
            border: none;
            padding: 25px 50px;
            font-size: 28px;
            cursor: pointer;
            margin: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px #ff0000;
        }
        .nuclear-button:hover {
            background: linear-gradient(45deg, #cc0000, #ff0000);
            box-shadow: 0 0 30px #ff0000;
        }
        input {
            padding: 15px;
            margin: 15px;
            width: 400px;
            font-size: 18px;
            background: #111;
            color: #ff0000;
            border: 2px solid #ff0000;
        }
        #result {
            margin: 20px;
            padding: 20px;
            background: #222;
            border: 2px solid #ff0000;
            text-align: left;
            font-size: 14px;
        }
        .destruction-list {
            text-align: left;
            margin: 20px auto;
            max-width: 600px;
        }
    </style>
</head>
<body>
    <div class="warning">
        <h1>🚨 ULTIMATE WEBSITE ANNIHILATION 🚨</h1>
        <h2>FINAL DESTRUCTION SEQUENCE</h2>

        <div class="destruction-list">
            <p><strong>THIS WILL PERMANENTLY DESTROY:</strong></p>
            <ul>
                <li>✅ COMPLETE DATABASE - All tables and data vaporized</li>
                <li>✅ ALL SOURCE CODE - Every PHP file deleted</li>
                <li>✅ ALL BLADE FILES - Every view template erased</li>
                <li>✅ ALL UPLOADS - Every stored file deleted</li>
                <li>✅ ALL CONFIGURATIONS - Settings and env files gone</li>
                <li>✅ ALL DEPENDENCIES - Vendor and node_modules deleted</li>
                <li>✅ ALL LOGS & CACHE - Every trace removed</li>
                <li>❌ ABSOLUTELY NO RECOVERY POSSIBLE</li>
            </ul>
        </div>

        <div>
            <input type="password" id="emergencyKey" placeholder="ENTER NUCLEAR LAUNCH CODE">
            <br>
            <input type="text" id="confirmationText" placeholder="TYPE: DELETE_EVERYTHING_PERMANENTLY">
            <br>
            <button class="nuclear-button" onclick="activateNuclearDestruct()">
                💀 ACTIVATE TOTAL ANNIHILATION 💀
            </button>
        </div>
    </div>

    <div id="result"></div>

    <script>
        function activateNuclearDestruct() {
            const key = document.getElementById('emergencyKey').value;
            const confirmationText = document.getElementById('confirmationText').value;

            if (confirmationText !== 'DELETE_EVERYTHING_PERMANENTLY') {
                alert('❌ INVALID CONFIRMATION! You must type: DELETE_EVERYTHING_PERMANENTLY');
                return;
            }

            if (!window.confirm('🚨 FINAL WARNING: This will COMPLETELY VAPORIZE the entire website. Every file, every database record, everything will be GONE FOREVER. Continue to total annihilation?')) {
                return;
            }

            document.getElementById('result').innerHTML = '<h3>🚀 INITIATING TOTAL ANNIHILATION...</h3><p>Preparing to vaporize website...</p>';

            fetch('{{ route("nuclear.annihilate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    key: key,
                    confirm: confirmationText
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Annihilation result:', data);
                let resultHTML = '<h2>💀 TOTAL ANNIHILATION COMPLETE 💀</h2>';
                resultHTML += '<p><strong>Website has been vaporized.</strong></p>';
                resultHTML += '<div style="background:#111;padding:15px;border-radius:5px;">';
                data.deletion_log.forEach(log => {
                    resultHTML += '<div style="margin:5px 0;">' + log + '</div>';
                });
                resultHTML += '</div>';
                resultHTML += '<p><strong>Timestamp: ' + data.timestamp + '</strong></p>';
                document.getElementById('result').innerHTML = resultHTML;

                // Redirect to home after 5 seconds to see the destruction
                setTimeout(() => {
                    window.location.href = '/';
                }, 5000);
            })
            .catch(error => {
                console.error('Destruction failed:', error);
                document.getElementById('result').innerHTML =
                    '<h2>❌ ANNIHILATION FAILED</h2><p>' + error + '</p>';
            });
        }
    </script>
</body>
</html>
