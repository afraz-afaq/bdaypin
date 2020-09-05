<?php
namespace common\components;



use Kreait\Firebase\Factory;

class FirebaseHelper
{

    public static function getFirebaseMessaging(){

        $factory = (new Factory)
            ->withServiceAccount(\Yii::getAlias('@backend').'/web/firebase/firebase-service-account.json')
            // The following line is optional if the project id in your credentials file
            // is identical to the subdomain of your Firebase project. If you need it,
            // make sure to replace the URL with the URL of your project.
            ->withDatabaseUri("https://bdaypin-74fe3.firebaseio.com/");
        return $factory->createMessaging();
    }

}

?>