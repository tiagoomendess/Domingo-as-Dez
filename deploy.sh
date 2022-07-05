echo "#########################"
echo "#   Starting deploy!"   #"
echo "#########################"

echo "Taking application down"
php artisan down

echo "Changing folder owner to root user"
chown root ../DomingoAsDez/ -R

echo "Getting the Code from repository"
git reset --hard
git pull

echo "Clearing Cache"
php artisan cache:clear

echo "Composer install"
composer install

echo "Running migrations"
php artisan migrate --env=production --force

echo "Setting folder owner back to nginx user"
chmod 755 -R ../DomingoAsDez/
chown www-data ../DomingoAsDez/ -R

echo "Trying to open application"
php artisan up
echo "Application Up!"

echo "#################"
echo "#   Finished!   #"
echo "#################"
echo " "
