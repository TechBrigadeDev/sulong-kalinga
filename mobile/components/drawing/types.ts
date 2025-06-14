export interface DrawingCanvasProps {
    // Add any props if needed in the future
}

export interface OrientationOverlayProps {
    isVisible: boolean;
}

export interface DrawingAreaProps {
    currentPath: any; // Skia Path type
    hasStartedDrawing: boolean;
    onPathChange: (path: any) => void;
    onStartDrawing: () => void;
    canvasRef: React.RefObject<any>; // CanvasRef type
}

export interface ButtonBarProps {
    onBack: () => void;
    onClear: () => void;
    onSave: () => void;
    isSaving: boolean;
}
