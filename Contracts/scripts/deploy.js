const hre = require("hardhat");

async function main() {
    // 1️⃣ Récupérer le contrat
    const LandTitleRegistry = await hre.ethers.getContractFactory("LandTitleRegistry");

    // 2️⃣ Déployer le contrat
    const landTitleRegistry = await LandTitleRegistry.deploy();
    await landTitleRegistry.waitForDeployment(); // ✅ Correcte syntaxe

    // 3️⃣ Afficher l'adresse du contrat déployé
    console.log("LandTitleRegistry deployed to:", await landTitleRegistry.getAddress());
}

// 🔥 Exécuter le script avec gestion des erreurs
main().catch((error) => {
    console.error(error);
    process.exitCode = 1;
});
