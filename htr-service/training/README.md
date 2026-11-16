# OAHTD/Kamrup training preparation

The service does not bundle OAHTD or any citizen record. Prepare an authorized dataset locally.

## Required manifest

Create `train.csv` and `validation.csv`:

```csv
image_path,text
images/000001.png,অসমীয়া transcription exactly as verified
images/000002.png,পট্টা নং ১৮৭
```

Use line/field crops rather than full multi-column pages for the first model. Keep writer-level separation between training and validation data to prevent the same handwriting appearing in both sets.

## Recommended pilot

- 5,000 to 10,000 verified line/field crops for the first domain model
- At least 20% held out for validation
- Include clear, faded, stamped, overwritten and skewed samples
- Record Patta, Dag, Year, Pattadar and Guardian fields separately
- Never train on unverified OCR output

The generated model directory must contain a Transformers-compatible `VisionEncoderDecoderModel`, image processor and tokenizer. Place it under:

```text
models/assamese-land-record-htr/
```

The API deliberately returns HTTP 503 until a real model exists.
