const gridHorraire = document.querySelector('#gridHorraire');
    const labelHorraire = document.querySelector('#horraireLabel');

    const HORAIRES = ['08h00', '10h00', '11h30', '14h30', '16h00'];

    const today = new Date();
    const maxDate = new Date(today);
    maxDate.setDate(maxDate.getDate() + 7);


    const fmt = d => d.toISOString().split('T')[0];

    flatpickr('#calendarContainer', {
        locale: 'fr',
        inline: true,
        dateFormat: 'Y-m-d',
        minDate: fmt(today),
        maxDate: fmt(maxDate),
        defaultDate: fmt(today),

        onChange: function(selectedDate, dateStr) {

            gridHorraire.innerHTML = '';
            AffichageHorraire(dateStr);
        }


    });

    function AffichageHorraire(date) {

        HORAIRES.forEach(h => {

            const btn = document.createElement('button');
            btn.className = 'horraire-btn';
            btn.textContent = h;
            labelHorraire.innerHTML = `Créneaux disponibles pour le <strong>${date}</strong>`;
            gridHorraire.appendChild(btn);
            btn.addEventListener('click', () => {
                reservationSelected(h, date);
            })

        })

    }

    function setValue(input, value) {
        document.getElementById(input).value = value;
        document.getElementById('filter-form').submit();
    }

    function patientSelected(patientId, patientName, patientChambre, patientService, patientLastName) {

        const recap = document.getElementById('recapPatient');
        const infos = document.getElementById('infos-sec-patient');


        recap.textContent = 'Patient : ' + patientName + ' ' + patientLastName;

        recap.dataset.patientId = patientId;
        
        infos.textContent = `Chambre ${patientChambre} — Service ${patientService}`;
        document.getElementById('input-patient-id').value = patientId;



    }

    function reservationSelected(horraireValue, dateValue) {

        if (horraireValue && dateValue) {

            document.getElementById('recapRdv').textContent = `Retrait prévu le ${dateValue} à ${horraireValue}`;
            document.getElementById('recapRdv').classList.remove('empty');
            document.getElementById('input-date-retrait').value = ConvetToSqlFormat(dateValue, horraireValue);
            document.getElementById('btnConfirm').disabled = false;
            document.getElementById('btnConfirm').style.cursor = 'pointer';
        }

    }
    function ConvetToSqlFormat(date, horraire) {
    const time = horraire.replace('h', ':') + ':00'; 
    return `${date} ${time}`;
}