import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import frLocale from '@fullcalendar/core/locales/fr';  // Importation de la locale française
import '../styles/page/bookform.scss';
import '../styles/page/modal.scss';

let calendar; // Variable globale pour stocker l'instance du calendrier

// Fonction pour obtenir toutes les dates entre deux dates
function getDatesInRange(startDate, endDate) {
    const dates = [];
    let currentDate = new Date(startDate); // Convertir la date en objet Date
    const end = new Date(endDate); // Convertir la date en objet Date

    while (currentDate <= end) { // Boucler jusqu'à la date de fin
        dates.push(new Date(currentDate).toISOString().split('T')[0]); // Ajouter la date au tableau
        currentDate.setDate(currentDate.getDate() + 1); // Incrémenter la date
    }

    return dates; // Retourner le tableau de dates
}

// Fonction pour initialiser FullCalendar
function initializeCalendar() {
    const calendarEl = document.getElementById('calendar');
    let reservedDates = []; // Tableau pour stocker les dates réservées
    let reservedHalfDays = {}; // Objet pour stocker les demi-journées réservées

    if (calendarEl) { // Vérifier si l'élément #calendar existe
        calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin, interactionPlugin], // Charger les plugins
            initialView: 'dayGridMonth',    // Vue initiale du calendrier
            locale: frLocale,  // Utilisation de la locale française
            dateClick: function(info) { // Gérer le clic sur une date
                const selectedDate = info.dateStr.split('T')[0]; // Extraire la date
                const forfait = calendarEl.dataset.forfait;  // Récupérer le forfait à partir de l'attribut data

                // Vérifier si la date est passée ou est aujourd'hui
                const today = new Date().toISOString().split('T')[0];
                if (selectedDate <= today) {
                    alert("Les réservations pour aujourd'hui ou les dates passées ne sont pas autorisées.");
                    return;
                }

                if (!reservedDates.includes(selectedDate)) { // Vérifier si la date est réservée
                    openBookingForm(info.dateStr, forfait); // Ouvrir le formulaire de réservation
                } else if (reservedHalfDays[selectedDate] && reservedHalfDays[selectedDate].length < 2) { // Vérifier si une demi-journée est disponible
                    openBookingForm(info.dateStr, forfait); // Ouvrir le formulaire de réservation
                }
            },
            events: { // Récupérer les événements à partir de l'API
                url: '/bookings',
                extraParams: function() {
                    return {
                        timezone: 'local' // Utiliser le fuseau horaire local
                    };
                },
                failure: function() {
                    alert('Il y a eu une erreur lors de la récupération des événements.'); // Afficher une alerte en cas d'erreur
                },
                success: function(data) {
                    reservedDates = []; // Réinitialiser les dates réservées
                    reservedHalfDays = {}; // Réinitialiser les demi-journées réservées

                    data.forEach(event => { // Boucler sur les événements
                        const eventDates = getDatesInRange(event.start, event.end); // Obtenir toutes les dates entre la date de début et la date de fin
                        reservedDates = reservedDates.concat(eventDates); // Ajouter les dates au tableau

                        // Stocker les demi-journées réservées
                        if (event.period) { // Vérifier si la période est précisée
                            const eventDate = event.start.split('T')[0]; // Extraire la date
                            if (!reservedHalfDays[eventDate]) { // Vérifier si la date existe dans l'objet
                                reservedHalfDays[eventDate] = []; // Initialiser un tableau vide
                            }
                            reservedHalfDays[eventDate].push(event.period); // Ajouter la période au tableau
                        }
                    });

                    return data.map(event => { // Retourner les événements
                        const eventDuration = (new Date(event.end) - new Date(event.start)) / (1000 * 60 * 60); // Durée en heures
                        if (eventDuration >= 9) { // Vérifier si la durée est supérieure ou égale à 9 heures
                            event.classNames = ['long-event']; // Appliquer une classe CSS spécifique
                        }
                        return event; // Retourner l'événement
                    });
                }
            },
            eventClassNames: function(arg) { // Personnaliser les classes CSS des événements
                const eventDuration = (new Date(arg.event.end) - new Date(arg.event.start)) / (1000 * 60 * 60); // Durée en heures
                if (eventDuration >= 9) { // Vérifier si la durée est supérieure ou égale à 9 heures
                    return ['long-event']; // Retourne la classe 'long-event' pour les événements de longue durée
                }
                return []; // Retourne un tableau vide pour les autres événements
            },
            eventContent: function(arg) { 
                // Personnaliser l'affichage des événements pour inclure l'heure de début et le titre
                let timeText = ''; // Initialiser le texte de l'heure
                if (arg.event.start) { // Vérifier si l'heure de début est précisée
                    const start = new Date(arg.event.start); // Convertir l'heure de début en objet Date
                    timeText = start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }); // Formater l'heure
                }
                return { html: `<div class="fc-event-time">${timeText}</div><div class="fc-event-title">${arg.event.title}</div>` }; // Retourner le contenu HTML
            },
            selectable: true, // Activer la sélection
            selectAllow: function(selectInfo) { // Autoriser la sélection
                const selectedDate = selectInfo.startStr.split('T')[0]; // Extraire la date sélectionnée

                // Vérifier si la date est passée ou est aujourd'hui
                const today = new Date().toISOString().split('T')[0];
                if (selectedDate <= today) {
                    return false; // Empêcher la sélection des dates passées ou d'aujourd'hui
                }

                if (reservedDates.includes(selectedDate)) { // Vérifier si la date est réservée
                    return false; // Empêcher la sélection
                }

                const period = selectInfo.start.getHours() < 12 ? 'morning' : 'afternoon'; // Déterminer la période

                if (reservedHalfDays[selectedDate] && reservedHalfDays[selectedDate].includes(period)) { // Vérifier si la demi-journée est réservée
                    return false; // Empêcher la sélection
                }

                return true; // Autoriser la sélection
            },
            selectOverlap: function(event) { // Gérer le chevauchement de la sélection
                return event.rendering === 'background'; // Autoriser le chevauchement pour les événements de fond
            }
        });

        calendar.render(); // Afficher le calendrier
    }
}

// Fonction pour ouvrir le formulaire de réservation
function openBookingForm(date, forfait) { // Prendre la date et le forfait en paramètres
    fetch(`/booking/form?date=${date}&forfait=${forfait}`) // Récupérer le formulaire de réservation
        .then(response => response.text()) // Convertir la réponse en texte
        .then(html => { // Manipuler le HTML
            document.getElementById('bookingFormContainer').innerHTML = html; // Injecter le formulaire dans la modal
            document.getElementById('bookingModal').style.display = 'block'; // Afficher la modal

            // Ajouter la date dans un champ caché du formulaire
            const form = document.querySelector('#bookingForm'); // Sélectionner le formulaire
            const hiddenDateInput = document.createElement('input'); // Créer un champ caché
            hiddenDateInput.type = 'hidden'; // Définir le type du champ
            hiddenDateInput.name = 'bookAt'; // Définir le nom du champ
            hiddenDateInput.value = date + " 08:00:00"; // Ajouter l'heure fixe
            form.appendChild(hiddenDateInput); // Ajouter le champ au formulaire

            // Initialiser les champs
            const periodField = document.getElementById('booking_period').closest('.mb-3'); // Sélectionner le champ de période
            const productField = document.getElementById('booking_product'); // Sélectionner le champ de produit

            // Fonction pour basculer la visibilité du champ de période
            function togglePeriodField() { // Fonction pour basculer la visibilité du champ de période
                if (parseInt(productField.value) === 1) { // Vérifier si le produit est un forfait 1/2journée
                    periodField.style.display = ''; // Afficher le champ de période
                } else {
                    periodField.style.display = 'none'; // Masquer le champ de période
                }
            }

            togglePeriodField(); // Appeler la fonction pour initialiser l'affichage

            // Ajouter un écouteur d'événement pour le champ de produit
            productField.addEventListener('change', togglePeriodField);

            // Sélectionner la période par défaut si aucune période n'est précisée
            if (parseInt(productField.value) === 3) { // Vérifier si le produit est un forfait 1/2journée
                const periodSelect = document.getElementById('booking_period');
                if (!periodSelect.value) { // Vérifier si aucune période n'est précisée
                    periodSelect.value = 'morning'; // Valeur par défaut pour la période
                }
            }

            // Gérer l'affichage des participants
            const isGroupCheckbox = document.getElementById('booking_isGroup');
            const participantsContainer = document.getElementById('participantsContainer');
            const participantList = document.getElementById('participant-list');
            const addParticipantButton = document.getElementById('add-participant');
            let index = participantList.children.length;

            function toggleParticipantsContainer() { // Fonction pour basculer la visibilité du conteneur de participants
                if (isGroupCheckbox.checked) {
                    participantsContainer.style.display = '';
                } else {
                    participantsContainer.style.display = 'none';
                }
            }

            // Initial check
            toggleParticipantsContainer(); // Appeler la fonction pour initialiser l'affichage

            // Add event listener
            isGroupCheckbox.addEventListener('change', toggleParticipantsContainer); // Ajouter un écouteur d'événement pour le champ de groupe

            // Add participant
            addParticipantButton.addEventListener('click', () => { // Ajouter un écouteur d'événement pour le bouton d'ajout de participant
                let newLi = document.createElement('li'); // Créer un élément li
                let newWidget = participantList.dataset.prototype.replace(/__name__/g, index++); // Remplacer le placeholder __name__ par l'index
                newLi.innerHTML = newWidget + '<button type="button" class="remove-participant btn btn-danger">Supprimer</button>'; // Ajouter le widget et le bouton de suppression
                participantList.appendChild(newLi); // Ajouter l'élément li à la liste
            });

            // Effacer un participant
            participantList.addEventListener('click', (e) => {
                if (e.target && e.target.classList.contains('remove-participant')) { // Vérifier si le clic est sur le bouton de suppression
                    e.target.parentElement.remove(); // Supprimer l'élément parent
                }
            });

            // Vérification avant la soumission du formulaire
            form.addEventListener('submit', function(event) { // Ajouter un écouteur d'événement pour la soumission du formulaire
                event.preventDefault(); // Empêcher la soumission par défaut

                const formData = new FormData(form); // Créer un objet FormData à partir du formulaire
                fetch('/book', { // Envoyer les données du formulaire à l'API
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json()) // Convertir la réponse en JSON
                .then(data => {
                    console.log(data);
                    if (data.error) {
                        const errorContainer = document.getElementById('errorContainer');
                        if (errorContainer) { 
                            errorContainer.innerHTML = data.error;
                        } else {
                            const errorDiv = document.createElement('div');
                            errorDiv.id = 'errorContainer';
                            errorDiv.style.color = 'red';
                            errorDiv.innerHTML = data.error;
                            form.prepend(errorDiv);
                        }
                    } else { 
                        if (data.redirectUrl) {
                            window.location.href = data.redirectUrl;
                        } else {
                            document.getElementById('bookingModal').style.display = 'none';
                            alert('Afin de valider votre réservation, merci de procéder au paiement.');
                
                            // Appel AJAX pour créer la session de paiement et redirection
                            fetch(`/create-checkout-session/${data.invoiceId}`)
                                .then(response => response.json())
                                .then(session => {
                                    if (session.error) {
                                        alert(session.error);
                                    } else {
                                        const stripe = Stripe('pk_test_51OyW1HP5v3Vi9EzZ2fStuwYNqNCs1tPnEnMb3qRE6DxEml4GN3HnC5UvxzIOEO9wSwEe8s8nzZlOhBiczCDbbae700OPASw4fz'); // Utilise ta clé publique Stripe
                                        stripe.redirectToCheckout({ sessionId: session.id });
                                    }
                                })
                                .catch(error => console.warn('Error creating Stripe checkout session:', error));
                        }
                    }
                })
                .catch(error => console.warn('Error submitting the form:', error)); // Afficher une alerte en cas d'erreur
            });
        })
        .catch(error => console.warn('Error fetching the form:', error)); // Afficher une alerte en cas d'erreur
}

// Fermer la modal
document.querySelector('.close').onclick = function() { // Ajouter un écouteur d'événement pour le bouton de fermeture
    document.getElementById('bookingModal').style.display = 'none';
};

// Fermer la modal en cliquant en dehors de celle-ci
window.onclick = function(event) { // Ajouter un écouteur d'événement pour le clic sur la fenêtre
    if (event.target === document.getElementById('bookingModal')) {
        document.getElementById('bookingModal').style.display = 'none';
    }
};

// Initialiser FullCalendar lors du chargement de la page
document.addEventListener('DOMContentLoaded', function() { // Ajouter un écouteur d'événement pour le chargement du DOM
    initializeCalendar();
});
