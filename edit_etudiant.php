<?php
require 'db.php';
require 'auth.php';
require_login();

if (!isset($_GET['APO'])) {
    echo "ID manquant"; exit;
}

$APO = intval($_GET['APO']);

// جلب بيانات الطالب
$stmt = $pdo->prepare("SELECT * FROM etudiants_diplomé WHERE CODAPO = ?");
$stmt->execute([$APO]);
$etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$etudiant) {
    echo "Étudiant introuvable"; exit;
}

// بعد submit
if (isset($_POST['submit'])) {
    $nom = $_POST['Nom'];
    $prenom = $_POST['Prenom'];
    $cne = $_POST['cne'];
    $cin = $_POST['CIN'];
    $date_naiss = $_POST['DATE_NAIS'];
    $sexe = $_POST['Sexe'];
    $lieux = $_POST['Lieux_de_nais'];
    $ni = $_POST['NI'];
    $filiere = $_POST['Filiere'];
    $diplome = $_POST['Diplome'];
    $pays = $_POST['Pays'];
    $hand = $_POST['Hand'];

    $stmt = $pdo->prepare("UPDATE etudiants_diplomé SET Nom=?, Prenom=?, cne=?, CIN=?, DATE_NAIS=?, Sexe=?, Lieux_de_nais=?, NI=?, Filiere=?, Diplome=?, Pays=?, Hand=? WHERE id=?");
    if ($stmt->execute([$nom,$prenom,$cne,$cin,$date_naiss,$sexe,$lieux,$ni,$filiere,$diplome,$pays,$hand,$id])) {
        header("Location: etudiants_diplomé.php?msg=updated");
        exit;
    } else {
        echo "Erreur: " . implode(", ", $stmt->errorInfo());
    }
}
include 'header.php';
include 'layout_top.php';
?>


<form method="POST" class="max-w-1xl mx-auto p-2 bg-white border rounded shadow mt-2 space-y-2">
    <h2 class="text-sm font-semibold mb-1 text-center">تعديل بيانات الطالب</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block mb-1 font-medium">Nom</label>
            <input type="text" name="Nom" value="<?= htmlspecialchars($etudiant['Nom']) ?>" required
                   class="w-full p-1 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label class="block mb-1 font-medium">Prénom</label>
            <input type="text" name="Prenom" value="<?= htmlspecialchars($etudiant['Prenom']) ?>" required
                   class="w-full p-1  border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label class="block mb-1 font-medium">CNE</label>
            <input type="text" name="cne" value="<?= htmlspecialchars($etudiant['cne']) ?>" required
                   class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label class="block mb-1 font-medium">CIN</label>
            <input type="text" name="CIN" value="<?= htmlspecialchars($etudiant['CIN']) ?>"
                   class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label class="block mb-1 font-medium">Date Nais</label>
            <input type="text" name="DATE_NAIS" value="<?= htmlspecialchars($etudiant['DATE_NAIS']) ?>"
                   class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label class="block mb-1 font-medium">Sexe</label>
            <input type="text" name="Sexe" value="<?= htmlspecialchars($etudiant['Sexe']) ?>"
                   class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label class="block mb-1 font-medium">Lieux de Nais</label>
            <input type="text" name="Lieux_de_nais" value="<?= htmlspecialchars($etudiant['Lieux_de_nais']) ?>"
                   class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label class="block mb-1 font-medium">NI</label>
            <input type="text" name="NI" value="<?= htmlspecialchars($etudiant['NI']) ?>"
                   class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label class="block mb-1 font-medium">Filiere</label>
            <input type="text" name="Filiere" value="<?= htmlspecialchars($etudiant['Filiere']) ?>"
                   class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label class="block mb-1 font-medium">Diplome</label>
            <input type="text" name="Diplome" value="<?= htmlspecialchars($etudiant['Diplome']) ?>"
                   class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label class="block mb-1 font-medium">Pays</label>
            <input type="text" name="Pays" value="<?= htmlspecialchars($etudiant['Pays']) ?>"
                   class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label class="block mb-1 font-medium">HAND</label>
            <input type="text" name="Hand" value="<?= htmlspecialchars($etudiant['Hand']) ?>"
                   class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
    </div>

    <div class="text-center ">
        <button type="submit" name="submit"
                class="px-7 py-1 bg-blue-500 text-white font-semibold rounded hover:bg-blue-600 transition">
            تعديل
        </button>
    </div>
</form>
