{{-- Page-template dropdown, populated from the active theme's template/ folder
     (plus "default"). Pass ['selected' => <current template>]; defaults to "default". --}}
@php
    $__theme = optional(site_information('theme'))->option_value ?: 'default';
    $__dir = resource_path('views/theme/'.$__theme.'/template');
    $__templates = ['default' => 'Default'];
    if (is_dir($__dir)) {
        foreach (glob($__dir.'/*.blade.php') as $__file) {
            $__name = basename($__file, '.blade.php');
            $__templates[$__name] = ucfirst($__name);
        }
    }
@endphp
{{ Form::select('post_template', $__templates, $selected ?? 'default', ['class' => 'form-control']) }}
