<?php

namespace App\Admin;

/**
 * One editable field on a content record.
 *
 * The console renders the form from these and the controller validates from
 * them, so a field cannot appear on screen without rules behind it or be
 * validated into a form nobody can fill in.
 */
final class ContentField
{
    /**
     * @param  'text'|'textarea'|'lines'|'number'|'date'|'select'|'checkbox'  $type
     * @param  array<int, string>  $rules
     * @param  array<string, string>  $options  value => label, for select
     */
    private function __construct(
        public string $name,
        public string $label,
        public string $type,
        public array $rules,
        public ?string $help = null,
        public array $options = [],
        public bool $wide = false,
    ) {}

    /** @param array<int, string> $rules */
    public static function text(string $name, string $label, array $rules = ['nullable', 'string', 'max:255'], ?string $help = null): self
    {
        return new self($name, $label, 'text', $rules, $help);
    }

    /** @param array<int, string> $rules */
    public static function textarea(string $name, string $label, array $rules = ['nullable', 'string', 'max:4000'], ?string $help = null): self
    {
        return new self($name, $label, 'textarea', $rules, $help, wide: true);
    }

    /**
     * A list stored as a JSON array, edited one item per line.
     *
     * Textareas are the honest control here: these lists are short, they are
     * reordered by retyping, and a repeater widget would be more machinery than
     * the job needs.
     *
     * @param  array<int, string>  $rules
     */
    public static function lines(string $name, string $label, ?string $help = null): self
    {
        return new self($name, $label, 'lines', ['nullable', 'string', 'max:4000'],
            $help ?? 'One per line. Blank lines are dropped.', wide: true);
    }

    /** @param array<int, string> $rules */
    public static function number(string $name, string $label, array $rules = ['nullable', 'integer', 'min:0'], ?string $help = null): self
    {
        return new self($name, $label, 'number', $rules, $help);
    }

    public static function date(string $name, string $label, ?string $help = null): self
    {
        return new self($name, $label, 'date', ['nullable', 'date'], $help);
    }

    /** @param array<string, string> $options */
    public static function select(string $name, string $label, array $options, bool $required = true, ?string $help = null): self
    {
        $rules = [$required ? 'required' : 'nullable', 'in:'.implode(',', array_keys($options))];

        return new self($name, $label, 'select', $rules, $help, $options);
    }

    public static function checkbox(string $name, string $label, ?string $help = null): self
    {
        return new self($name, $label, 'checkbox', ['boolean'], $help, wide: true);
    }

    public function required(): self
    {
        $rules = array_values(array_diff($this->rules, ['nullable']));

        if (! in_array('required', $rules, true)) {
            array_unshift($rules, 'required');
        }

        return new self($this->name, $this->label, $this->type, $rules, $this->help, $this->options, $this->wide);
    }

    /** Present the stored value in the shape the control expects. */
    public function toForm(mixed $value): string
    {
        return match ($this->type) {
            'lines' => is_array($value) ? implode("\n", $value) : (string) $value,
            'date' => $value ? $value->format('Y-m-d') : '',
            'select' => $value instanceof \BackedEnum ? (string) $value->value : (string) $value,
            default => (string) $value,
        };
    }

    /** Turn what was submitted back into what the column holds. */
    public function toModel(mixed $input): mixed
    {
        return match ($this->type) {
            'lines' => collect(preg_split('/\r\n|\r|\n/', (string) $input))
                ->map(fn (string $line) => trim($line))
                ->filter()
                ->values()
                ->all(),
            'checkbox' => (bool) $input,
            'number' => $input === null || $input === '' ? null : (int) $input,
            default => $input === '' ? null : $input,
        };
    }
}
