<?php

namespace App\Livewire;

use App\Models\Shipment;
use App\Models\Route;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class ReceptionManager extends Component
{
    use WithPagination, WithFileUploads;

    public $route;
    public $showReceptionModal = false;
    public $showLoadingModal = false;
    public $showPhotoModal = false;
    public $selectedShipment;
    public $receptionPhoto;
    public $receptionNotes = '';
    public $loadingNotes = '';
    public $photoToPreview = '';

    protected $rules = [
        'receptionPhoto' => 'required|image|max:2048',
        'receptionNotes' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'receptionPhoto.required' => 'La foto de la caja es obligatoria.',
        'receptionPhoto.image' => 'El archivo debe ser una imagen.',
        'receptionPhoto.max' => 'La imagen no puede ser mayor a 2MB.',
        'receptionNotes.max' => 'Las notas no pueden exceder 500 caracteres.',
    ];

    public function mount(Route $route)
    {
        $this->route = $route;
    }

    public function render()
    {
        $shipments = $this->route->shipments()
            ->with(['client', 'box', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.reception-manager', [
            'shipments' => $shipments
        ]);
    }

    public function showReceptionForm(Shipment $shipment)
    {
        if ($shipment->reception_status !== 'pending') {
            session()->flash('error', 'Esta caja ya ha sido recepcionada.');
            return;
        }

        $this->selectedShipment = $shipment;
        $this->reset(['receptionPhoto', 'receptionNotes']);
        $this->showReceptionModal = true;
    }

    public function showLoadingForm(Shipment $shipment)
    {
        if (!$shipment->canBeLoaded()) {
            session()->flash('error', 'Esta caja debe estar recepcionada con foto antes de ser cargada.');
            return;
        }

        $this->selectedShipment = $shipment;
        $this->reset(['loadingNotes']);
        $this->showLoadingModal = true;
    }

    public function closeReceptionModal()
    {
        $this->showReceptionModal = false;
        $this->reset(['selectedShipment', 'receptionPhoto', 'receptionNotes']);
    }

    public function closeLoadingModal()
    {
        $this->showLoadingModal = false;
        $this->reset(['selectedShipment', 'loadingNotes']);
    }

    public function showPhotoPreview($photoPath)
    {
        try {
            // Intentar mostrar el modal
            $this->photoToPreview = $photoPath;
            $this->showPhotoModal = true;
            
            // Verificar si el archivo existe y mostrar mensaje apropiado
            if (!Storage::exists($photoPath)) {
                session()->flash('warning', 'La foto no se encuentra disponible. Verifica que el archivo no haya sido eliminado.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al intentar mostrar la foto: ' . $e->getMessage());
        }
    }

    public function closePhotoModal()
    {
        $this->showPhotoModal = false;
        $this->photoToPreview = '';
    }


    public function markAsReceived()
    {
        $this->validate();

        // Guardar la foto
        $photoPath = $this->receptionPhoto->store('reception-photos', 'public');

        // Actualizar el envío
        $this->selectedShipment->update([
            'reception_status' => 'received',
            'received_at' => now(),
            'reception_photo_path' => $photoPath,
            'reception_notes' => $this->receptionNotes,
            'received_by' => auth()->id(),
        ]);

        session()->flash('success', 'Caja marcada como recepcionada exitosamente.');
        $this->closeReceptionModal();
    }

    public function markAsLoaded()
    {
        $this->validate([
            'loadingNotes' => 'nullable|string|max:500',
        ]);

        $this->selectedShipment->update([
            'reception_status' => 'loaded',
            'loaded_at' => now(),
            'loaded_by' => auth()->id(),
        ]);

        if ($this->loadingNotes) {
            $this->selectedShipment->update([
                'reception_notes' => $this->selectedShipment->reception_notes . "\n\nNotas de carga: " . $this->loadingNotes
            ]);
        }

        session()->flash('success', 'Caja marcada como cargada exitosamente.');
        $this->closeLoadingModal();
    }

    public function updateReceptionStatus(Shipment $shipment, $status)
    {
        $availableStatuses = $shipment->getAvailableReceptionStatuses();
        
        if (!in_array($status, $availableStatuses)) {
            session()->flash('error', 'No se puede cambiar a ese estado desde el estado actual.');
            return;
        }

        $updateData = ['reception_status' => $status];

        switch ($status) {
            case 'received':
                $updateData['received_at'] = now();
                $updateData['received_by'] = auth()->id();
                break;
            case 'loaded':
                $updateData['loaded_at'] = now();
                $updateData['loaded_by'] = auth()->id();
                break;
            case 'in_transit':
                // Puedes agregar lógica adicional aquí
                break;
            case 'delivered':
                // Puedes agregar lógica adicional aquí
                break;
        }

        $shipment->update($updateData);
        session()->flash('success', 'Estado actualizado exitosamente.');
    }

    public function getReceptionStatusCounts()
    {
        return [
            'pending' => $this->route->shipments()->where('reception_status', 'pending')->count(),
            'received' => $this->route->shipments()->where('reception_status', 'received')->count(),
            'loaded' => $this->route->shipments()->where('reception_status', 'loaded')->count(),
            'in_transit' => $this->route->shipments()->where('reception_status', 'in_transit')->count(),
            'delivered' => $this->route->shipments()->where('reception_status', 'delivered')->count(),
        ];
    }

    public function getPhotoSize($photoPath)
    {
        try {
            if (Storage::exists($photoPath)) {
                return number_format(Storage::size($photoPath) / 1024, 1) . ' KB';
            }
        } catch (\Exception $e) {
            // Log error si es necesario
        }
        return 'Unknown';
    }

    public function getPhotoLastModified($photoPath)
    {
        try {
            if (Storage::exists($photoPath)) {
                $timestamp = Storage::lastModified($photoPath);
                if ($timestamp) {
                    return \Carbon\Carbon::createFromTimestamp($timestamp)->format('M d, Y');
                }
            }
        } catch (\Exception $e) {
            // Log error si es necesario
        }
        return 'Unknown';
    }

    public function getPhotoLastModifiedTime($photoPath)
    {
        try {
            if (Storage::exists($photoPath)) {
                $timestamp = Storage::lastModified($photoPath);
                if ($timestamp) {
                    return \Carbon\Carbon::createFromTimestamp($timestamp)->format('H:i:s');
                }
            }
        } catch (\Exception $e) {
            // Log error si es necesario
        }
        return 'Unknown';
    }

    public function getPhotoUrl($photoPath)
    {
        try {
            if (Storage::exists($photoPath)) {
                return Storage::url($photoPath);
            }
        } catch (\Exception $e) {
            // Log error si es necesario
        }
        return null;
    }

    public function isPhotoAccessible($photoPath)
    {
        try {
            return Storage::exists($photoPath);
        } catch (\Exception $e) {
            return false;
        }
    }
}