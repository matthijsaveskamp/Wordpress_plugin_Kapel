# Kapel Footer Gallery 2 - WordPress Plugin

Een WordPress plugin waarmee je foto's uit je mediabibliotheek kunt selecteren en deze in een roterende footer gallery kunt tonen.

## Functies

-  Selecteer meerdere foto's uit je WordPress mediabibliotheek
-  Automatische rotatie tussen foto's met vloeiende overgangen
-  Instelbare overgangssnelheid en weergaveduur
-  Keuze tussen fade en slide effecten
-  Navigatie pijlen om handmatig tussen foto's te wisselen
-  Volledig responsive design
-  Eenvoudige plaatsing via shortcode of widget
-  Drag & drop om foto volgorde aan te passen
-  Aangepaste tekst per foto toevoegen
-  Optie voor willekeurige volgorde of vaste volgorde
-  Bestandsnaam weergave onder foto's (optioneel)

## Installatie

1. Upload de `kapel-footer-gallery` map naar de `/wp-content/plugins/` directory
2. Activeer de plugin via het 'Plugins' menu in WordPress
3. Ga naar Instellingen → Footer Gallery om foto's toe te voegen en instellingen aan te passen

## Gebruik

### Via Shortcode

Plaats de volgende shortcode in je posts, pages of templates:

```
[kapel_footer_gallery]
```

Met aangepaste instellingen:

```
[kapel_footer_gallery height="300px" transition="slide"]
```

### Via PHP in templates

```php
<?php echo do_shortcode('[kapel_footer_gallery]'); ?>
```

### Via Widget

1. Ga naar Weergave → Widgets
2. Zoek naar "Kapel Footer Gallery"
3. Sleep de widget naar je gewenste sidebar/footer area
4. Configureer de instellingen en sla op

## Shortcode Parameters

- `height` - Hoogte van de gallery (standaard: 200px)
- `transition` - Type overgang: "fade" of "slide" (standaard: fade)

## Instellingen

De plugin heeft de volgende instellingen (beschikbaar onder Instellingen → Footer Gallery):

- **Galerij Foto's**: Selecteer foto's uit je mediabibliotheek en voeg optionele aangepaste tekst toe per foto
- **Overgangssnelheid**: Duur van de overgang tussen foto's (in milliseconden, standaard: 1000)
- **Weergaveduur**: Hoe lang elke foto wordt getoond (in milliseconden, standaard: 5000)
- **Toon bestandsnaam**: Toon de bestandsnaam of aangepaste tekst onder elke foto
- **Willekeurige volgorde**: Toon foto's in willekeurige volgorde bij elke paginalading (of vaste volgorde)

## Bestandsstructuur

```
kapel-footer-gallery/
├── kapel-footer-gallery.php  # Hoofd plugin bestand
├── uninstall.php             # Cleanup bij deinstallatie
├── assets/
│   ├── css/
│   │   ├── admin.css         # Admin styling
│   │   └── frontend.css      # Frontend styling
│   └── js/
│       ├── admin.js          # Admin functionaliteit (incl. jQuery UI sortable)
│       └── frontend.js       # Frontend rotatie en navigatie
└── README.md
```

## Vereisten

- WordPress 5.0 of hoger
- PHP 5.6 of hoger (aanbevolen: PHP 7.4+)

## Ondersteuning

Voor vragen of problemen, neem contact op via matthijsaveskamp@gmail.com.

## Licentie

GPL v2 or later

## Changelog

### 1.0.0
- Eerste release
- Basis functionaliteit voor foto selectie en rotatie
- Admin instellingen pagina
- Shortcode en widget ondersteuning
- Fade en slide overgangen

### 2.0.0
- Toegevoegd: Aangepaste tekst per foto in de galerij
- Toegevoegd: Optie voor willekeurige volgorde of vaste volgorde
- Toegevoegd: Bestandsnaam weergave optioneel onder foto's
- Toegevoegd: Navigatie pijlen voor handmatige bediening
- Verbeterd: Input sanitization voor betere beveiliging
- Verbeterd: Nonce verificatie via WordPress settings API
- Verbeterd: jQuery UI Sortable correct geladen als dependency
- Verbeterd: Betrouwbaardere bestandsnaam extractie
- Verbeterd: Aangepaste info modal in plaats van alert()
- Toegevoegd: PHP en WordPress versie checks
- Toegevoegd: Textdomain loading voor vertalingen
- Toegevoegd: uninstall.php voor database cleanup
- Toegevoegd: Multisite ondersteuning in uninstall
