"use client";
import { useEffect, useRef, useState } from "react";
import { api } from "./api-client";
import { subscribeOrderChanges } from "./order-realtime";
// Read-only refresh; provider verification and all state transitions stay in Laravel.
export function useOrderPolling(code, enabled, onUpdate) {
  const manual = useRef(null);
  const [state, setState] = useState({
    stopped: false,
    error: false,
    refreshing: false,
    lastUpdated: null,
  });
  useEffect(() => {
    setState({
      stopped: false,
      error: false,
      refreshing: false,
      lastUpdated: null,
    });
    if (!code || !enabled) return;
    const abort = new AbortController();
    let inFlight = false;
    let attempts = 0;
    async function refresh(force = false) {
      if (
        document.hidden ||
        inFlight ||
        (!force && attempts >= 30) ||
        abort.signal.aborted
      )
        return;
      inFlight = true;
      attempts++;
      setState((s) => ({ ...s, refreshing: true }));
      try {
        const { data } = await api(`/orders/${encodeURIComponent(code)}`, {
          signal: abort.signal,
        });
        if (!abort.signal.aborted) {
          onUpdate(data);
          setState({
            stopped: attempts >= 30,
            error: false,
            refreshing: false,
            lastUpdated: Date.now(),
          });
        }
      } catch {
        if (!abort.signal.aborted)
          setState((s) => ({ ...s, error: true, stopped: attempts >= 30 }));
        // Keep the last verified state on transient failures. Never infer paid/unpaid.
      } finally {
        inFlight = false;
        if (!abort.signal.aborted)
          setState((s) => ({ ...s, refreshing: false }));
      }
    }
    manual.current = () => refresh(true);
    let stopRealtime = () => {};
    subscribeOrderChanges(code, () => refresh(true))
      .then((stop) => {
        if (abort.signal.aborted) stop();
        else stopRealtime = stop;
      })
      .catch(() => {}); // Polling/manual refresh remain available during provider failure.
    const focus = () => refresh();
    const timer = window.setInterval(() => refresh(), 10000);
    window.addEventListener("focus", focus);
    return () => {
      abort.abort();
      stopRealtime();
      window.clearInterval(timer);
      manual.current = null;
      window.removeEventListener("focus", focus);
    };
  }, [code, enabled, onUpdate]);
  return { ...state, refresh: () => manual.current?.() };
}
