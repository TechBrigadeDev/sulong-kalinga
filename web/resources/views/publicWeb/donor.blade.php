<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Signature</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 mt-5">
                <div class="card">
                    <div class="card-header">Digital Signature Form</div>
                    <div class="card-body">
                        <!-- Single Form for Both Signatures -->
                        <form id="signatureForm">
                            <div class="row">
                                <!-- First Signature Column -->
                                <div class="col-md-6">
                                    <div class="form-group mt-3">
                                        <label>Draw First Signature</label>
                                        <div id="signature-pad-1" class="signature-pad">
                                            <div class="signature-pad-body">
                                                <canvas id="canvas1" style="border: 1px solid #ced4da; width: 100%; height: 200px;"></canvas>
                                            </div>
                                            <div class="signature-pad-footer text-end mt-2">
                                                <button type="button" id="clear-signature-1" class="btn btn-danger">Clear</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Second Signature Column -->
                                <div class="col-md-6">
                                    <div class="form-group mt-3">
                                        <label>Draw Second Signature</label>
                                        <div id="signature-pad-2" class="signature-pad">
                                            <div class="signature-pad-body">
                                                <canvas id="canvas2" style="border: 1px solid #ced4da; width: 100%; height: 200px;"></canvas>
                                            </div>
                                            <div class="signature-pad-footer text-end mt-2">
                                                <button type="button" id="clear-signature-2" class="btn btn-danger">Clear</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary mt-3">Submit Both Signatures</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Signature Pad JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Initialize Signature Pads
            const canvas1 = document.getElementById("canvas1");
            const canvas2 = document.getElementById("canvas2");

            const signaturePad1 = new SignaturePad(canvas1);
            const signaturePad2 = new SignaturePad(canvas2);

            // Resize canvas to fit the container
            function resizeCanvas(canvas, signaturePad) {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePad.clear(); // Clear the canvas after resizing
            }

            // Resize both canvases on page load and window resize
            function initializeCanvas() {
                resizeCanvas(canvas1, signaturePad1);
                resizeCanvas(canvas2, signaturePad2);
            }

            window.addEventListener("resize", initializeCanvas);
            initializeCanvas();

            // Clear First Signature
            document.getElementById("clear-signature-1").addEventListener("click", function () {
                signaturePad1.clear();
            });

            // Clear Second Signature
            document.getElementById("clear-signature-2").addEventListener("click", function () {
                signaturePad2.clear();
            });

            // Handle Form Submission
            document.getElementById("signatureForm").addEventListener("submit", function (e) {
                e.preventDefault();

                if (signaturePad1.isEmpty()) {
                    alert("Please draw the first signature.");
                    return;
                }

                if (signaturePad2.isEmpty()) {
                    alert("Please draw the second signature.");
                    return;
                }

                // Save signatures as base64 images
                const signatureDataURL1 = signaturePad1.toDataURL();
                const signatureDataURL2 = signaturePad2.toDataURL();

                // Log the signatures (you can send these to the backend)
                console.log("First Signature (Base64):", signatureDataURL1);
                console.log("Second Signature (Base64):", signatureDataURL2);

                alert("Both signatures submitted successfully!");
            });
        });
    </script>
</body>
</html>