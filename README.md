# RL Google Maps

Add a Google Map to your WordPress site using a simple shortcode.

## Usage

Insert the following shortcode into your page or post content:

```
[rl_google_map api_key="YOUR_GOOGLE_MAPS_API_KEY"]
```

### Available attributes

- `api_key` – **required** – your Google Maps API key
- `lat` – latitude (default: `52.231998990860056`)
- `lng` – longitude (default: `21.00603791534297`)
- `zoom` – map zoom level (default: `14`)
- `height` – map height in pixels for desktop (default: `400`)
- `mobile_height` – map height in pixels for mobile devices (default: `300`)
- `marker` – URL to a custom marker icon. If not set in shortcode or admin settings, the plugin's default SVG marker will be used: `[plugin-url]/assets/img/default-marker.svg`
- `marker_width` – marker width in px (default: `48`)
- `marker_height` – marker height in px (default: `48`)
- `phone` – phone number (default: `+48 000 000 000`)
- `address` – address displayed in the info window (default: `plac Defilad 1, 00-901 Warszawa`)
- `title` – title in the info window (default: `Company Name`)
- `email` – email address (default: `contact@domain.com`)

**Note:**  
If the "Default Marker URL" field in the admin settings is empty and you do not provide a `marker` attribute in the shortcode, the plugin will always use its built-in default marker SVG.

### Custom Map Styles

You can set custom map styles in the plugin settings page. Paste your JSON styles in the "Custom Map Styles" textarea. You can browse and copy styles from [Snazzy Maps](https://snazzymaps.com/explore).

### Responsive Maps

The plugin automatically adjusts the map height on mobile devices. You can set different heights for desktop and mobile in the admin settings or using the `height` and `mobile_height` shortcode attributes. The mobile height will be applied when the screen width is less than 768px.

### Example usage

```
[rl_google_map api_key="YOUR_GOOGLE_MAPS_API_KEY" lat="50.055024169833054" lng="19.934704713801594" zoom="16" height="500" mobile_height="350" marker="https://example.com/marker.png" title="My Company" phone="+48 123 456 789" address="Wawel 5, 31-001 Krakow" email="office@company.com"]
```

The shortcode will automatically display a map with a marker and an info window containing your contact details.

## TO DO / Planned Features

- [X] Single marker with info window and contact details
- [ ] Option to add multiple markers via shortcode attributes or JSON
- [X] Custom map themes/styles selection
- [ ] Option to disable/enable map controls (zoom, street view, etc.)
- [ ] Cluster support for multiple markers
- [ ] Option to display directions between points
- [ ] Gutenberg block for easier map insertion
- [X] Admin settings page for global defaults (API key, marker, etc.)
- [ ] Support for marker popups with custom HTML
- [X] Responsive and mobile-friendly improvements
- [ ] Accessibility enhancements
- [X] Language/translation support
- [ ] Caching and performance optimizations