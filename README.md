Le projet : ReservPro — plateforme de réservation multi-activités

Pensé pour le marché ivoirien : salles de sport, salons de coiffure, coworking, consultations (médecin, coach, etc.) — un gérant peut créer son "espace" et gérer ses créneaux.

Acteurs (rôles)
Admin (toi, plateforme) — supervise tout
Gérant — possède un ou plusieurs établissements, gère créneaux/tarifs/staff
Client — réserve, paie, annule selon règles
Fonctionnalités clés (avec la vraie logique métier)
Module	Logique métier à implémenter
Établissements & créneaux	Un gérant définit des créneaux récurrents (horaires ouverture, durée, capacité max) → génération automatique des disponibilités
Réservation	Détection de conflits (double réservation, capacité dépassée), verrouillage optimiste ou transaction DB
Annulation	Règles selon délai (ex: >24h = remboursement total, <24h = pénalité 50%)
Paiement	Intégration CinetPay ou Wave (Mobile Money) — webhook de confirmation
Rappels automatiques	Job planifié (Laravel Scheduler) qui envoie un rappel J-1 par email/WhatsApp
Rôles & permissions	Policies : un gérant ne voit/gère que SES établissements ; admin voit tout
Dashboard gérant	Taux d'occupation, revenus par période, no-show rate
Avis clients	Un avis seulement après réservation "terminée" (pas avant)
Stack technique
Laravel 11+, MySQL/PostgreSQL
Sanctum (API tokens) si tu veux une API mobile derrière, ou Breeze si web classique — je recommande Sanctum + API pure, ça te prépare direct à connecter du Flutter dessus plus tard (cohérence avec ta phase 3)
Laravel Scheduler + Queues pour les rappels et l'expiration des réservations non payées
Policies + Gates pour le RBAC
Pest pour les tests des règles métier critiques (conflits, annulation, paiement)
