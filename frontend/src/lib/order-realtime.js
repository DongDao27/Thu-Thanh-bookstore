import { api } from "./api-client.js";

// Realtime is an invalidation hint. Only the authenticated GET supplies order state.
export async function subscribeOrderChanges(code, onChange, options = {}) {
  const key = options.key ?? process.env.NEXT_PUBLIC_PUSHER_APP_KEY;
  if (!key || !/^[A-Za-z0-9-]{1,40}$/.test(code)) return () => {};
  const Pusher = options.Pusher ?? (await import("pusher-js")).default;
  const request = options.request ?? api;
  const abort = new AbortController();
  const channelName = `private-orders.${code}`;
  const client = new Pusher(key, {
    cluster:
      options.cluster ?? process.env.NEXT_PUBLIC_PUSHER_APP_CLUSTER ?? "ap1",
    forceTLS: true,
    enabledTransports: ["ws", "wss"],
    disableStats: true,
    channelAuthorization: {
      customHandler: ({ socketId, channelName: requested }, callback) => {
        if (requested !== channelName || abort.signal.aborted) {
          callback(new Error("Invalid order channel"), null);
          return;
        }
        request("/broadcasting/auth", {
          method: "POST",
          body: { socket_id: socketId, channel_name: requested },
          signal: abort.signal,
        }).then(
          ({ data }) => callback(null, data),
          (error) => callback(error, null),
        );
      },
    },
  });
  const channel = client.subscribe(channelName);
  let revision = 0;
  let timer;
  const refresh = () => {
    clearTimeout(timer);
    timer = setTimeout(() => {
      if (!abort.signal.aborted) onChange();
    }, 250);
  };
  const changed = (event) => {
    if (
      event?.order_code !== code ||
      !Number.isSafeInteger(event.version) ||
      event.version <= revision
    )
      return;
    revision = event.version;
    refresh();
  };
  channel.bind("order.changed", changed);
  channel.bind("pusher:subscription_succeeded", refresh);
  // Failed auth/disconnect leave the existing bounded polling/manual refresh active.
  return () => {
    abort.abort();
    clearTimeout(timer);
    channel.unbind("order.changed", changed);
    channel.unbind("pusher:subscription_succeeded", refresh);
    client.unsubscribe(channelName);
    client.disconnect();
  };
}
