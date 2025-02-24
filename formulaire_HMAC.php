<?php

// --------------- DÉCLARATION DES VARIABLES ---------------

$pbx_site = 'votre n° de site';															//variable de test 9999999
$pbx_rang = 'votre n° de rang';															//variable de test 95
$pbx_identifiant = 'votre n° d identifiant site';										//variable de test 123456789
$pbx_total = 'votre montant';															//variable de test 200
// Suppression des points ou virgules dans le montant						
	$pbx_total = str_replace(",", "", $pbx_total);
	$pbx_total = str_replace(".", "", $pbx_total);

$pbx_cmd = 'votre n° de commande';														//variable de test cmd_test1
$pbx_porteur = 'email de l acheteur';													//variable de test test@test.fr

// Paramétrage de l'url de retour back office site (notification de paiement IPN) :
$pbx_repondre_a = 'http://www.votre-site.extention/page-de-back-office-site';

// Paramétrage des données retournées via l'IPN :
$pbx_retour = 'Mt:M;Ref:R;Auto:A;Erreur:E';

// Paramétrage des urls de redirection navigateur client après paiement :
$pbx_effectue = 'http://www.votre-site.extention/accepte.php';
$pbx_annule = 'http://www.votre-site.extention/annule.php';
$pbx_refuse = 'http://www.votre-site.extention/refuse.php';

// On récupère la date au format ISO-8601 :
$dateTime = date("c");

// Nombre de produit envoyé dans PBX_SHOPPINGCART :
$pbx_nb_produit = 'nombre de produit dans le panier';									//variable de test 5
// Construction de PBX_SHOPPINGCART :
$pbx_shoppingcart = "<?xml version=\"1.0\" encoding=\"utf-8\"?><shoppingcart><total><totalQuantity>".$pbx_nb_produit."</totalQuantity></total></shoppingcart>";

// Valeurs envoyées dans PBX_BILLING :
$pbx_prenom_fact = 'prenom de l utilisateur de facturation';							//variable de test Jean-Marie
$pbx_nom_fact = 'nom de l utilisateur de facturation';									//variable de test Thomson
$pbx_adresse1_fact = 'ligne1 de l adresse de facturation';								//variable de test 1 rue de Paris
$pbx_adresse2_fact = 'ligne2 de l adresse de facturation';								//variable de test <vide>
$pbx_zipcode_fact = 'code postal de l adresse de facturation';							//variable de test 75001
$pbx_city_fact = 'ville de l adresse de facturation';									//variable de test Paris
$pbx_country_fact = 'code pays iso-3166-1 numérique de l adresse de facturation';		//variable de test 250 (pour la France)
// Construction de PBX_BILLING :
$pbx_billing = "<?xml version=\"1.0\" encoding=\"utf-8\"?><Billing><Address><FirstName>".$pbx_prenom_fact."</FirstName>".
				"<LastName>".$pbx_nom_fact."</LastName><Address1>".$pbx_adresse1_fact."</Address1>".
				"<Address2>".$pbx_adresse2_fact."</Address2><ZipCode>".$pbx_zipcode_fact."</ZipCode>".
				"<City>".$pbx_city_fact."</City><CountryCode>".$pbx_country_fact."</CountryCode>".
				"</Address></Billing>";

// --------------- SÉLÉCTION DE L'ENVIRRONEMENT ---------------
// Recette (paiements de test)  :
		$urletrans ="https://recette-tpeweb.e-transactions.fr/php/";

// Production (paiements réels) :
	// URL principale :
		// $urletrans ="https://tpeweb.e-transactions.fr/php/";
	// URL secondaire :
		// $urletrans ="https://tpeweb1.e-transactions.fr/php/";

// --------------- RÉCUPÉRATION DE LA CLÉ HMAC ---------------
// Connection à la base de données
// mysql_connect...
// On récupère la clé secrète HMAC (stockée dans une base de données par exemple) et que l'on renseigne dans la variable $hmackey;
// $hmackey = '4642EDBBDFF9790734E673A9974FC9DD4EF40AA2929925C40B3A95170FF5A578E7D2579D6074E28A78BD07D633C0E72A378AD83D4428B0F3741102B69AD1DBB0';
$hmackey = 'votre clé générée depuis le back office Vision';

// --------------- TRAITEMENT DES VARIABLES ---------------

// On crée la chaîne à hacher sans URLencodage
$msg = "PBX_SITE=".$pbx_site.
"&PBX_RANG=".$pbx_rang.
"&PBX_IDENTIFIANT=".$pbx_identifiant.
"&PBX_TOTAL=".$pbx_total.
"&PBX_DEVISE=978".
"&PBX_CMD=".$pbx_cmd.
"&PBX_PORTEUR=".$pbx_porteur.
"&PBX_REPONDRE_A=".$pbx_repondre_a.
"&PBX_RETOUR=".$pbx_retour.
"&PBX_EFFECTUE=".$pbx_effectue.
"&PBX_ANNULE=".$pbx_annule.
"&PBX_REFUSE=".$pbx_refuse.
"&PBX_HASH=SHA512".
"&PBX_TIME=".$dateTime.
"&PBX_SHOPPINGCART=".$pbx_shoppingcart.
"&PBX_BILLING=".$pbx_billing;
// echo $msg;

// Si la clé est en ASCII, On la transforme en binaire
$binKey = pack("H*", $hmackey);

// On calcule l'empreinte (à renseigner dans le paramètre PBX_HMAC) grâce à la fonction hash_hmac et //
// la clé binaire
// On envoi via la variable PBX_HASH l'algorithme de hachage qui a été utilisé (SHA512 dans ce cas)
// Pour afficher la liste des algorithmes disponibles sur votre environnement, décommentez la ligne //
// suivante
// print_r(hash_algos());
$hmac = strtoupper(hash_hmac('sha512', $msg, $binKey));

// La chaîne sera envoyée en majuscule, d'où l'utilisation de strtoupper()
// On crée le formulaire à envoyer
// ATTENTION : l'ordre des champs dans le formulaire est extrêmement important, il doit
// correspondre exactement à l'ordre des champs dans la chaîne hachée.
?>

<!------------------ ENVOI DES INFORMATIONS A e-Transactions (Formulaire) ------------------>

<form method="POST" action="<?php echo $urletrans; ?>">
<input type="hidden" name="PBX_SITE" value="<?php echo $pbx_site; ?>">
<input type="hidden" name="PBX_RANG" value="<?php echo $pbx_rang; ?>">
<input type="hidden" name="PBX_IDENTIFIANT" value="<?php echo $pbx_identifiant; ?>">
<input type="hidden" name="PBX_TOTAL" value="<?php echo $pbx_total; ?>">
<input type="hidden" name="PBX_DEVISE" value="978">
<input type="hidden" name="PBX_CMD" value="<?php echo $pbx_cmd; ?>">
<input type="hidden" name="PBX_PORTEUR" value="<?php echo $pbx_porteur; ?>">
<input type="hidden" name="PBX_REPONDRE_A" value="<?php echo $pbx_repondre_a; ?>">
<input type="hidden" name="PBX_RETOUR" value="<?php echo $pbx_retour; ?>">
<input type="hidden" name="PBX_EFFECTUE" value="<?php echo $pbx_effectue; ?>">
<input type="hidden" name="PBX_ANNULE" value="<?php echo $pbx_annule; ?>">
<input type="hidden" name="PBX_REFUSE" value="<?php echo $pbx_refuse; ?>">
<input type="hidden" name="PBX_HASH" value="SHA512">
<input type="hidden" name="PBX_TIME" value="<?php echo $dateTime; ?>">
<input type="hidden" name="PBX_SHOPPINGCART" value="<?php echo htmlspecialchars($pbx_shoppingcart); ?>">
<input type="hidden" name="PBX_BILLING" value="<?php echo htmlspecialchars($pbx_billing); ?>">
<input type="hidden" name="PBX_HMAC" value="<?php echo $hmac; ?>">
<input type="submit" value="Envoyer">
</form>