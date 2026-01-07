# Script de gestion de sécurité - Alpha Community (Windows)
# Utilisation: .\security-management.ps1 -Command init

param(
    [string]$Command = "help"
)

$ProjectRoot = Split-Path -Parent $MyInvocation.MyCommand.Definition

Write-Host "════════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "  GESTION DE SÉCURITÉ - ALPHA COMMUNITY (Windows)" -ForegroundColor Cyan
Write-Host "════════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""

# ===== FONCTION: Vérifier la sécurité =====
function Check-Security {
    Write-Host "[CHECK] Vérification de la sécurité..." -ForegroundColor Yellow
    & php "$ProjectRoot\security-check.php"
}

# ===== FONCTION: Initialiser le projet =====
function Init-Project {
    Write-Host "[INIT] Initialisation du projet..." -ForegroundColor Yellow
    
    # Créer .env depuis .env.example
    if (!(Test-Path "$ProjectRoot\.env")) {
        Copy-Item "$ProjectRoot\.env.example" "$ProjectRoot\.env"
        Write-Host "[OK] Fichier .env créé" -ForegroundColor Green
    } else {
        Write-Host "[!] Fichier .env existe déjà" -ForegroundColor Yellow
    }
    
    # Créer répertoire logs
    if (!(Test-Path "$ProjectRoot\logs")) {
        New-Item -ItemType Directory -Path "$ProjectRoot\logs" | Out-Null
        Write-Host "[OK] Répertoire logs créé" -ForegroundColor Green
    }
    
    # Créer répertoire uploads
    if (!(Test-Path "$ProjectRoot\config\uploads")) {
        New-Item -ItemType Directory -Path "$ProjectRoot\config\uploads" | Out-Null
        Write-Host "[OK] Répertoire uploads créé" -ForegroundColor Green
    }
    
    # Vérifier la sécurité
    Check-Security
}

# ===== FONCTION: Générer une clé sécurisée =====
function Generate-Key {
    Write-Host "[KEYGEN] Génération de clé sécurisée..." -ForegroundColor Yellow
    Write-Host ""
    
    $Key = & php -r "echo bin2hex(random_bytes(32));"
    Write-Host "Nouvelle clé: " -NoNewline
    Write-Host $Key -ForegroundColor Green
    Write-Host ""
    Write-Host "Ajouter dans .env:" -ForegroundColor Green
    Write-Host "JWT_SECRET=$Key"
    Write-Host ""
}

# ===== FONCTION: Nettoyer les logs =====
function Clean-Logs {
    param(
        [int]$Days = 30
    )
    
    Write-Host "[CLEAN] Nettoyage des logs..." -ForegroundColor Yellow
    
    $cutoffDate = (Get-Date).AddDays(-$Days)
    $LogsPath = "$ProjectRoot\logs"
    
    if (Test-Path $LogsPath) {
        Get-ChildItem -Path $LogsPath -Filter "*.log*" -File | 
            Where-Object { $_.LastWriteTime -lt $cutoffDate } | 
            Remove-Item -Force
        
        Write-Host "[OK] Logs plus anciens que $Days jours supprimés" -ForegroundColor Green
    }
}

# ===== FONCTION: Afficher usage =====
function Show-Usage {
    Write-Host "Utilisation: .\security-management.ps1 -Command [commande]"
    Write-Host ""
    Write-Host "Commandes disponibles:" -ForegroundColor Cyan
    Write-Host "  init              - Initialiser le projet (créer .env, dossiers, etc.)"
    Write-Host "  check             - Vérifier la sécurité"
    Write-Host "  keygen            - Générer une clé sécurisée"
    Write-Host "  clean-logs        - Nettoyer les logs (30 jours par défaut)"
    Write-Host "  help              - Afficher cette aide"
    Write-Host ""
}

# ===== MAIN =====
switch ($Command.ToLower()) {
    "init" {
        Init-Project
        break
    }
    "check" {
        Check-Security
        break
    }
    "keygen" {
        Generate-Key
        break
    }
    "clean-logs" {
        Clean-Logs
        break
    }
    "help" {
        Show-Usage
        break
    }
    default {
        Write-Host "[ERREUR] Commande inconnue: $Command" -ForegroundColor Red
        Write-Host ""
        Show-Usage
        exit 1
    }
}

Write-Host ""
