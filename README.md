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
- `marker` – URL to a custom marker icon (default: `[plugin-url]/assets/img/default-marker.svg`)
- `marker_width` – marker width in px (default: `48`)
- `marker_height` – marker height in px (default: `48`)
- `phone` – phone number (default: `+48 000 000 000`)
- `address` – address displayed in the info window (default: `plac Defilad 1, 00-901 Warszawa`)
- `title` – title in the info window (default: `Company Name`)
- `email` – email address (default: `contact@domain.com`)

### Example usage

```
[rl_google_map api_key="YOUR_GOOGLE_MAPS_API_KEY" lat="50.055024169833054" lng="19.934704713801594" zoom="16" marker="https://example.com/marker.png" title="My Company" phone="+48 123 456 789" address="Wawel 5, 31-001 Krakow" email="office@company.com"]
```

The shortcode will automatically display a map with a marker and an info window containing your contact details.

## TO DO / Planned Features

- [X] Single marker with info window and contact details
- [ ] Option to add multiple markers via shortcode attributes or JSON
- [ ] Custom map themes/styles selection
- [ ] Option to disable/enable map controls (zoom, street view, etc.)
- [ ] Cluster support for multiple markers
- [ ] Option to display directions between points
- [ ] Gutenberg block for easier map insertion
- [ ] Admin settings page for global defaults (API key, marker, etc.)
- [ ] Support for marker popups with custom HTML
- [ ] Responsive and mobile-friendly improvements
- [ ] Accessibility enhancements
- [ ] Language/translation support
- [ ] Caching and performance optimizations