<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  echo "<script>alert('Please log in first!'); window.location.href='login.php';</script>";
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>F1 Teams 2025</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>

    .bg-mclaren { background-color: #ff8000;  }
    .bg-ferrari { background-color: #da1212;  }
    .bg-mercedes { background-color: #00c0b5;  }
    .bg-redbull { background-color: #002f6c; }
    .bg-aston { background-color: #0A7968; }
    .bg-alpine { background-color: #fd4bc7; }
    .bg-williams { background-color: #00A3E0; }
    .bg-kick { background-color: #52E252; }
    .bg-rb { background-color: #1930A2; }

    .text-kick { color: black; }
    
  </style>
</head>
<body class="m-0 font-sans bg-gray-900 text-center text-white min-h-screen">

<header>
  <img src="image/F1AcademyLogo.png" alt="F1 Academy Logo" class="mt-8 w-48 mx-auto">
  <p class="text-gray-400 mb-8">Select your F1 Academy team</p>
</header>

<div class="team-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 px-10 pb-16">
  
  <div class="team-card bg-mclaren rounded-xl relative overflow-hidden p-5 flex flex-col justify-between transition duration-300 ease-in-out hover:scale-[1.02] cursor-pointer" onclick="selectTeam('McLaren')">
    <div class="team-name text-3xl font-bold mb-3">McLaren</div>
    <img class="logo absolute top-5 right-5 w-10" src="image/mclaren (1)-modified.png" alt="McLaren Logo">
    <img class="car w-full max-h-32 object-contain mt-5" src="image/2025mclarencarright.avif" alt="McLaren Car">
  </div>

  <div class="team-card bg-ferrari rounded-xl relative overflow-hidden p-5 flex flex-col justify-between transition duration-300 ease-in-out hover:scale-[1.02] cursor-pointer" onclick="selectTeam('Ferrari')">
    <div class="team-name text-3xl font-bold mb-3">Ferrari</div>
    <img class="logo absolute top-5 right-5 w-10" src="image/Ferrari-removebg-preview.png" alt="Ferrari Logo">
    <img class="car w-full max-h-32 object-contain mt-5" src="image/2025ferraricarright.avif" alt="Ferrari Car">
  </div>

  <div class="team-card bg-mercedes rounded-xl relative overflow-hidden p-5 flex flex-col justify-between transition duration-300 ease-in-out hover:scale-[1.02] cursor-pointer" onclick="selectTeam('Mercedes')">
    <div class="team-name text-3xl font-bold mb-3">Mercedes</div>
    <img class="logo absolute top-5 right-5 w-10" src="image/Mercedes-Logo.png" alt="Mercedes Logo">
    <img class="car w-full max-h-32 object-contain mt-5" src="image/2025mercedescarright.avif" alt="Mercedes Car">
  </div>

  <div class="team-card bg-redbull rounded-xl relative overflow-hidden p-5 flex flex-col justify-between transition duration-300 ease-in-out hover:scale-[1.02] cursor-pointer" onclick="selectTeam('Red Bull Racing')">
    <div class="team-name text-3xl font-bold mb-3">Red Bull Racing</div>
    <img class="logo absolute top-5 right-5 w-10" src="image/RedBull-removebg-preview.png" alt="Red Bull Logo">
    <img class="car w-full max-h-32 object-contain mt-5" src="image/2025redbullracingcarright.avif" alt="Red Bull Car">
  </div>

  <div class="team-card bg-aston rounded-xl relative overflow-hidden p-5 flex flex-col justify-between transition duration-300 ease-in-out hover:scale-[1.02] cursor-pointer" onclick="selectTeam('Aston Martin')">
    <div class="team-name text-3xl font-bold mb-3">Aston Martin</div>
    <img class="logo absolute top-5 right-5 w-10" src="image/aston_martini-removebg-preview.png" alt="Aston Martin Logo">
    <img class="car w-full max-h-32 object-contain mt-5" src="image/2025astonmartincarright.avif" alt="Aston Martin Car">
  </div>

  <div class="team-card bg-alpine rounded-xl relative overflow-hidden p-5 flex flex-col justify-between transition duration-300 ease-in-out hover:scale-[1.02] cursor-pointer" onclick="selectTeam('Alpine')">
    <div class="team-name text-3xl font-bold mb-3">Alpine</div>
    <img class="logo absolute top-5 right-5 w-10" src="image/alpine-logo-a-1955-removebg-preview.png" alt="Alpine Logo">
    <img class="car w-full max-h-32 object-contain mt-5" src="image/2025alpinecarright.avif" alt="Alpine Car">
  </div>

  <div class="team-card bg-williams rounded-xl relative overflow-hidden p-5 flex flex-col justify-between transition duration-300 ease-in-out hover:scale-[1.02] cursor-pointer" onclick="selectTeam('Williams')">
    <div class="team-name text-3xl font-bold mb-3">Williams</div>
    <img class="logo absolute top-5 right-5 w-10" src="image/Williams Racing Icon 2020.png" alt="Williams Logo">
    <img class="car w-full max-h-32 object-contain mt-5" src="image/2025williamscarright.avif" alt="Williams Car">
  </div>

  <div class="team-card bg-kick text-kick rounded-xl relative overflow-hidden p-5 flex flex-col justify-between transition duration-300 ease-in-out hover:scale-[1.02] cursor-pointer" onclick="selectTeam('Kick Sauber')">
    <div class="team-name text-3xl font-bold mb-3">Kick Sauber</div>
    <img class="logo absolute top-5 right-5 w-10" src="image/Kick-Logo-Logo--Streamline-Logos.png" alt="Kick Sauber Logo">
    <img class="car w-full max-h-32 object-contain mt-5" src="image/2025kicksaubercarright.avif" alt="Kick Sauber Car">
  </div>

  <div class="team-card bg-rb rounded-xl relative overflow-hidden p-5 flex flex-col justify-between transition duration-300 ease-in-out hover:scale-[1.02] cursor-pointer" onclick="selectTeam('RB Cash App')">
    <div class="team-name text-3xl font-bold mb-3">RB Cash App</div>
    <img class="logo absolute top-5 right-5 w-10" src="image/cash app.png" alt="RB Cash App Logo">
    <img class="car w-full max-h-32 object-contain mt-5" src="image/2025racingbullscarright.avif" alt="RB Cash App Car">
  </div>
</div>

<script>
function selectTeam(team) {
  if (confirm("You selected: " + team + ". Confirm?")) {
    fetch("save_team.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: "team=" + encodeURIComponent(team),
    })
    .then(response => response.text())
    .then(data => {
      alert("Team saved successfully! Now selecting sponsor.");
      window.location.href = "select_sponsor.php";
    })
    .catch(error => {
      alert("Error: " + error);
    });
  }
}
</script>

</body>
</html>