import test from "node:test";
import assert from "node:assert/strict";
import { subscribeOrderChanges } from "../src/lib/order-realtime.js";

test("private order subscription authorizes through CSRF API and only invalidates matching new versions", async () => {
  let config;
  let changes = 0;
  const handlers = new Map();
  const requests = [];
  let disconnected = false;
  class FakePusher {
    constructor(key, options) {
      assert.equal(key, "public-fixture");
      config = options;
    }
    subscribe(name) {
      assert.equal(name, "private-orders.TT-123");
      return {
        bind: (event, callback) => handlers.set(event, callback),
        unbind: (event) => handlers.delete(event),
      };
    }
    unsubscribe(name) {
      assert.equal(name, "private-orders.TT-123");
    }
    disconnect() {
      disconnected = true;
    }
  }
  const stop = await subscribeOrderChanges("TT-123", () => changes++, {
    key: "public-fixture",
    Pusher: FakePusher,
    request: async (path, options) => {
      requests.push({ path, ...options });
      return { data: { auth: "signed-fixture" } };
    },
  });
  assert.equal(config.forceTLS, true);
  await new Promise((resolve, reject) =>
    config.channelAuthorization.customHandler(
      { socketId: "123.456", channelName: "private-orders.TT-123" },
      (error, data) => {
        if (error) reject(error);
        else {
          assert.equal(data.auth, "signed-fixture");
          resolve();
        }
      },
    ),
  );
  assert.equal(requests[0].path, "/broadcasting/auth");
  assert.equal(requests[0].method, "POST");
  assert.deepEqual(requests[0].body, {
    socket_id: "123.456",
    channel_name: "private-orders.TT-123",
  });
  const event = handlers.get("order.changed");
  event({ order_code: "OTHER", version: 20 });
  event({ order_code: "TT-123", version: "2", payment_status: "paid" });
  await new Promise((resolve) => setTimeout(resolve, 280));
  assert.equal(changes, 0);
  event({ order_code: "TT-123", version: 2 });
  event({ order_code: "TT-123", version: 2 });
  event({ order_code: "TT-123", version: 1 });
  await new Promise((resolve) => setTimeout(resolve, 280));
  assert.equal(changes, 1);
  event({ order_code: "TT-123", version: 3 });
  stop();
  await new Promise((resolve) => setTimeout(resolve, 280));
  assert.equal(changes, 1);
  assert.equal(disconnected, true);
  assert.equal(requests[0].signal.aborted, true);
  assert.equal(handlers.size, 0);
});

test("disabled realtime does not load a client or request credentials", async () => {
  const stop = await subscribeOrderChanges("TT-123", () => assert.fail(), {
    key: "",
    request: () => assert.fail(),
    Pusher: class {
      constructor() {
        assert.fail();
      }
    },
  });
  stop();
});
