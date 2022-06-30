echo "#########################"
echo "#   Starting deploy!"   #"
echo "#########################"

echo "\n--- Taking application down ------------"
php artisan down

echo "\n--- Handing Permissions and Ownership to root ------------"
chmod 755 -R ../DomingoAsDez/
chown root ../DomingoAsDez/ -R

echo "\n--- Get Code -----------"
git reset --hard
git pull

echo "\n--- Clear Cache -----------"
php artisan cache:clear

echo "\n--- Composer install -----------"
composer install

echo "\n--- Running migrations ------------"
php artisan migrate --env=production

echo "\n--- Dealing with permissions ------------"
chmod 755 -R ../DomingoAsDez/
chown www-data ../DomingoAsDez/ -R

echo "\n--- Application Up! ------------"
php artisan up

echo "#################"
echo "#   Finished!   #"
echo "#################"
echo " "
