#!/bin/bash

# Exportez le PATH nécessaire
export PATH="/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin:/opt/homebrew/bin:/opt/homebrew/opt/mysql@8.0/bin:/opt/homebrew/Cellar/php/8.3.7/bin:/opt/homebrew/Cellar/php/8.3.7/sbin:$PATH"

# Exécutez la commande PHP pour nettoyer les réservations
/opt/homebrew/bin/php /Users/pierredenaes/Sites/bootcampsC/bin/console app:cleanup-booking
