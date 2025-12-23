<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    public function authorize()
    {
        // Vérifie que l'utilisateur est authentifié et a le rôle approprié
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'supervisor', 'manager']);
    }

    public function rules()
    {
        $bookId = $this->route('book')->id ?? null;

        return [
            // Champs texte principaux
            'title'                  => 'required|string|max:255',
            'title_ar'               => 'nullable|string|max:255',
            'barcode'                => 'required|string|max:255|unique:books,barcode,' . $bookId,
            'description'            => 'nullable|string|max:5000',

            // Relations
            'author_id'              => 'nullable|exists:authors,id',
            'category_id'            => 'nullable|exists:categories,id',
            'publisher_id'           => 'nullable|exists:publishers,id',

            // Infos du livre
            'pages'                  => 'nullable|integer|min:1|max:9999',
            'format'                 => 'nullable|in:hardcover,paperback,ebook',
            'publication_year'       => 'nullable|integer|min:1900|max:' . date('Y'),
            'edition_number'         => 'nullable|string|max:50',
            'book_condition'         => 'nullable|in:new,used_like_new,used_good',

            // Prix (retail_price modifiable pour tous)
            'retail_price'           => 'required|numeric|min:0',

            // Champs admin-only (pour Admin uniquement)
            'wholesale_price'        => 'nullable|numeric|min:0',
            'cost_price'             => 'nullable|numeric|min:0',
            'discount_percentage'    => 'nullable|numeric|min:0|max:100',
        ];
    }

    public function messages()
    {
        return [
            'title.required'           => 'Le titre est obligatoire.',
            'barcode.required'         => 'Le code-barres est obligatoire.',
            'barcode.unique'           => 'Ce code-barres existe déjà.',
            'author_id.exists'         => 'L\'auteur sélectionné n\'existe pas.',
            'category_id.exists'       => 'La catégorie sélectionnée n\'existe pas.',
            'publisher_id.exists'      => 'L\'éditeur sélectionné n\'existe pas.',
            'retail_price.required'    => 'Le prix de vente est obligatoire.',
            'retail_price.numeric'     => 'Le prix de vente doit être un nombre.',
            'retail_price.min'         => 'Le prix de vente doit être >= 0.',
            'pages.integer'            => 'Le nombre de pages doit être un entier.',
            'publication_year.integer' => 'L\'année doit être un entier.',
            'publication_year.min'     => 'L\'année doit être >= 1900.',
            'discount_percentage.max'  => 'La remise ne peut pas dépasser 100%.',
        ];
    }

    protected function prepareForValidation()
    {
        // Hook optionnel pour transformer les données avant validation
    }

    public function passedValidation()
    {
        // Si l'utilisateur n'est pas Admin, supprime les champs admin-only
        if (!auth()->user()->hasRole('admin')) {
            $this->offsetUnset('wholesale_price');
            $this->offsetUnset('cost_price');
            $this->offsetUnset('discount_percentage');
        }
    }
}
