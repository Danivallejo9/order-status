echo %time%

cd C:\SitiosWeb\App\app.samycosmetics.com\order-status\notification
php.exe -f index.php

echo %time%

timeout /t 150 /nobreak
