package middleware

import (
	"context"
	"net/http"
	"strings"
	"upcycleconnect/utils"
)

type contextKey string

const ClaimsKey contextKey = "claims"

// Auth vérifie le JWT et injecte les claims dans le contexte
func Auth(next http.HandlerFunc) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		authHeader := r.Header.Get("Authorization")
		if authHeader == "" || !strings.HasPrefix(authHeader, "Bearer ") {
			http.Error(w, "token manquant", http.StatusUnauthorized)
			return
		}

		tokenStr := strings.TrimPrefix(authHeader, "Bearer ")
		claims, err := utils.ParseToken(tokenStr)
		if err != nil {
			http.Error(w, "token invalide ou expiré", http.StatusUnauthorized)
			return
		}

		ctx := context.WithValue(r.Context(), ClaimsKey, claims)
		next(w, r.WithContext(ctx))
	}
}

// RequireRole vérifie que l'utilisateur a bien le rôle requis (après Auth)
func RequireRole(roleID int, next http.HandlerFunc) http.HandlerFunc {
	return Auth(func(w http.ResponseWriter, r *http.Request) {
		claims := r.Context().Value(ClaimsKey).(*utils.JWTClaims)
		if claims.IDRole != roleID {
			http.Error(w, "accès interdit", http.StatusForbidden)
			return
		}
		next(w, r)
	})
}
