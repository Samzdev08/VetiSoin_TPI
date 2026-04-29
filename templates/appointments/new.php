<?php
/**
 * Fichier : new.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Prise de rendez-vous
 */
?>
<div>

    <h1>Confirmation de réservation</h1>

    <div>
        <h2>Choisir la date de retrait</h2>

        <div id="calendarContainer"></div>

        <h3 id="horraireLabel">Créneaux disponibles</h3>
        <div id="horraireGrid"></div>

        <label for="lieu">Lieu de retrait</label>
        <select id="lieu" name="lieu">
            <option value="Vestiaire principal">Vestiaire principal</option>
            <option value="Secrétariat">Secrétariat</option>
        </select>
    </div>

    <aside>
        <h2>Récapitulatif de la réservation</h2>

        <div>
            <p><strong>Patient : </strong></p>
            <p></p>
        </div>

        <h3>Articles réservés</h3>
        <ul>
            <li></li>
        </ul>

        <div>
            <p>Rendez-vous sélectionné</p>
            <p id="recapRdv"></p>
            <p id="recapLieu"></p>
        </div>

        <button type="submit">Confirmer la réservation ✓</button>
    </aside>

</div>
<script></script>