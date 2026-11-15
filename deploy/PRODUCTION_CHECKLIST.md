# Production Readiness Checklist

## Network
- MySQL/Redis/private storage not internet-reachable.
- Administrative SSH/DB access through approved VPN/bastion.
- HTTPS only; HSTS enabled after certificate/host validation.

## Runtime
- APP_ENV=production
- APP_DEBUG=false
- SESSION_SECURE_COOKIE=true
- Strong APP_KEY and independent DMS_AUDIT_HMAC_KEY
- Redis recommended for cache/queue in production.

## Storage
- No public storage symlink for land-record documents.
- MASTER repository protected by storage versioning/immutability controls where available.
- Separate quarantine and document prefixes/buckets.

## Database
- Runtime account is least privilege.
- Migration account separate and available only during approved deployment.
- DBA credentials never present in application UI.

## Backup / DR
- Encrypted database backups.
- Repository/object backup or replication.
- Off-site/DR copy in separate failure domain.
- Manifest/checksum validation.
- Recorded restore test on an isolated environment.
- RPO/RTO approved by department BIA.

## Security
- MFA for privileged accounts before production.
- ClamAV/malware inspection installed before accepting production uploads.
- VAPT/authorization/IDOR/file-upload testing completed.
- Central/SIEM logging enabled.

## Operations
- Queue workers supervised.
- Laravel scheduler every minute.
- Alerts for backup failure, checksum mismatch, malware detection, queue backlog, storage capacity and DB availability.
