<?php
// Définir le chemin absolu vers le dossier racine du projet
define('ROOT_PATH', __DIR__);

// Définir l'URL de base
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$basePath = '/vent';
define('BASE_URL', $protocol . $host . $basePath);

// Inclure la configuration des sessions
require_once ROOT_PATH . '/app/Config/session.php';

// Inclure l'autoloader avec le chemin absolu
require_once ROOT_PATH . '/app/Config/autoloader.php';

// Récupérer l'URL depuis la requête
$url = '';
if (isset($_GET['url'])) {
    $url = explode('/', filter_var($_GET['url'], FILTER_SANITIZE_URL));
}

// Récupérer la méthode HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Fonction pour rediriger avec l'URL de base
function redirect($path) {
    global $basePath;
    header('Location: ' . $basePath . '/index.php?url=' . $path);
    exit;
}

// Contrôler la première valeur de l'URL
if (empty($url[0]) || $url[0] == 'accueil') {
    // Page d'accueil
    $controller = new \App\Controllers\HomeController();
    $controller->index();

} else if ($url[0] == 'user') {
    // Gestion des utilisateurs
    $controller = new \App\Controllers\UserController();
    
    if (isset($url[1])) {
        switch($url[1]) {
            case 'login':
                $controller->login();
                break;
            case 'register':
                $controller->register();
                break;
            case 'logout':
                $controller->logout();
                break;
            case 'commandes':
                $controller->orders();
                break;
            default:
                echo 'Action utilisateur non reconnue.';
        }
    } else {
        // Par défaut, rediriger vers la page de connexion
        redirect('user/login');
    }

} else if ($url[0] == 'admin') {
    // Gestion des produits par l'administrateur
    $controller = new \App\Controllers\AdminController();
    
    if (isset($url[1])) {
        switch($url[1]) {
            case 'produits':
                $controller->products();
                break;
            case 'ajouter-produit':
                $controller->addProduct();
                break;
            case 'modifier-produit':
                if (isset($url[2])) {
                    $controller->editProduct($url[2]);
                } else {
                    redirect('admin/produits');
                }
                break;
            case 'supprimer-produit':
                if (isset($url[2])) {
                    $controller->deleteProduct($url[2]);
                } else {
                    redirect('admin/produits');
                }
                break;
            default:
                redirect('admin/produits');
        }
    } else {
        redirect('admin/produits');
    }

} else if ($url[0] == 'products') {
    // Gestion des produits
    if ($method == 'GET') {
        if (isset($url[1]) && $url[1] == 'vetements-femme') {
            // FEMME : /products/vetements-femme ou /products/vetements-femme/4
            $controller = new \App\Controllers\ProductController();

            if (isset($url[2]) && is_numeric($url[2])) {
                // Détail produit femme
                $controller->showProductDetail($url[2]);
            } else {
                // Liste des produits femme
                $controller->showWomenall();
            }
        } elseif (isset($url[1]) && $url[1] == 'vetements-homme') {
            // HOMME : /products/vetements-homme ou /products/vetements-homme/4
            $controller = new \App\Controllers\ProductMenController();

            if (isset($url[2]) && is_numeric($url[2])) {
                // Détail produit homme
                $controller->showProductDetail($url[2]);
            } else {
                // Liste des produits homme
                $controller->showMenAll();
            }
        } else {
            echo 'Liste générale des produits ou route non reconnue.';
        }
    }

} else if ($url[0] == 'panier') {
    // Gestion du panier
    $controller = new \App\Controllers\CartController();
    
    if (isset($url[1])) {
        switch($url[1]) {
            case 'ajouter':
                $controller->add();
                break;
            case 'voir':
                $controller->view();
                break;
            case 'vider':
                $controller->clear();
                break;
            case 'payer':
                $controller->payer();
                break;
            case 'success':
                $controller->success();
                break;
            case 'select-address':
                $controller->selectAddress();
                break;
            default:
                echo 'Action du panier non reconnue.';
        }
    } else {
        // Par défaut, afficher le panier
        $controller->view();
    }

} else if ($url[0] == 'categorie') {
    // Gestion des catégories
    if ($method == 'GET') {
        echo 'Afficher les catégories';
    }

} else if ($url[0] == 'produit') {
    // Route alternative : /produit/4 (si tu veux la garder)
    if ($method == 'GET' && isset($url[1])) {
        $productId = (int) $url[1];
        $controller = new \App\Controllers\ProductController();
        $controller->showProductDetail($productId);
    } else {
        echo 'ID du produit manquant.';
    }

} else if ($url[0] == 'cart') {
    // Gestion du panier
    $controller = new \App\Controllers\CartController();
    
    if (isset($url[1])) {
        if ($url[1] == 'ajouter') {
            $controller->add();
        } else if ($url[1] == 'voir') {
            $controller->view();
        } else if ($url[1] == 'vider') {
            $controller->clear();
        } else {
            echo 'Action du panier non reconnue.';
        }
    } else {
        // Par défaut, afficher le panier
        $controller->view();
    }

} else if ($url[0] == 'address') {
    // Gestion des adresses
    $controller = new \App\Controllers\UserLoginController();
    
    if (isset($url[1])) {
        switch($url[1]) {
            case 'add':
                $controller->addAddress();
                break;
            default:
                echo 'Action d\'adresse non reconnue.';
        }
    } else {
        // Par défaut, rediriger vers la page d'accueil
        header('Location: /vent/index.php');
        exit;
    }

} else if ($url[0] == 'dashboard') {
    // Vérifier si l'utilisateur est connecté
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: /vent/index.php?url=user/login');
        exit;
    }
    // Afficher le tableau de bord
    require_once 'app/views/users/dashboard.php';

} else {
    // Page 404
    require_once 'app/router/erreur404.html';
}
