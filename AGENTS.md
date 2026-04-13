# AGENTS.md - import-converter-product-category

## Zweck & Verantwortung

Das `import-converter-product-category` Modul bietet **Product CSV zu Category CSV Konvertierung**. Es ist ein **Tier 5 Modul** und erweitert `import-converter`.

**Hauptverantwortung:**
- Transformation von Product CSV zu Category CSV
- Observer Pattern für Konvertierungs-Hooks
- Event-Driven für Konvertierungs-Prozesse
- Listener für Custom Processing

## Architektur & Design Patterns

### Kern-Klassen
- **ProductCategoryConverter**: Haupt-Converter-Klasse
- **ProductCategoryConverterObserver**: Observer für Hooks
- **ProductCategoryConverterListener**: Listener für Events

### Verwendete Patterns
- **Observer Pattern**: Für Konvertierungs-Hooks
- **Event-Driven**: Für Konvertierungs-Prozesse
- **Strategy Pattern**: Verschiedene Konvertierungs-Strategien

## Abhängigkeiten

### Externe Pakete
- **Keine**

### TechDivision Dependencies
- **import-product** ^26.0.0 - Product Importer
- **import-category** ^22.0.0 - Category Importer
- **import-converter** ^12.0.0 - Converter Framework

### Abhängig von diesem Modul (1 Reverse Dependency)
- **import-cli-simple** - Master CLI

## Wichtige Entry Points

### Converter Klassen
```php
// Product Category Converter
ProductCategoryConverter::convert($row): array
ProductCategoryConverter::getSubject(): SubjectInterface

// Converter Observer
ProductCategoryConverterObserver::handle($row): void
```

## Events & Extension Points

### Events
- **BeforeConversionEvent**: Vor Konvertierung
- **AfterConversionEvent**: Nach Konvertierung

### Listeners
- **ConversionListener**: Für Custom Processing

## Hints für KI-Agenten

### Wichtig zu verstehen
1. **Tier 5 Modul**: Erweitert Converter Framework
2. **Konvertierungs-fokussiert**: Product → Category CSV
3. **Observer Pattern**: Für Hooks
4. **Event-Driven**: Für Konvertierungs-Prozesse

## Bekannte Einschränkungen

- **Product-Category-Only**: Nur für Product-Category Links
- **CSV-Only**: Nur CSV-Format unterstützt

## Zusammenfassung

`import-converter-product-category` ist ein **Tier 5 Modul**, das Product CSV zu Category CSV Konvertierung bietet. Es erweitert den Converter Framework mit spezialisierter Funktionalität.

**Für Agenten:** Verstehe dieses Modul als **Product Category Converter** mit Observer und Event-Driven Architektur.
